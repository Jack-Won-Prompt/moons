<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PointTransaction;
use App\Models\Settlement;
use App\Models\UserCoupon;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private const SHIPPING_FREE = true; // 명품 무료배송

    public function checkout()
    {
        $items = CartItem::with('product')->where('customer_id', Auth::guard('web')->id())->get()
            ->filter(fn ($i) => $i->product);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', '장바구니가 비어 있습니다.');
        }

        $user = Auth::guard('web')->user();
        $coupons = UserCoupon::with('coupon')->where('user_id', $user->id)->usable()->get()
            ->filter(fn ($uc) => $uc->coupon && $uc->coupon->isValid());

        return view('customer.order.checkout', [
            'items'    => $items,
            'subtotal' => $items->sum('line_total'),
            'user'     => $user,
            'coupons'  => $coupons,
        ]);
    }

    /** 주문 생성 + 결제(PG 시뮬레이션) */
    public function place(Request $request)
    {
        $data = $request->validate([
            'receiver_name'  => ['required', 'string', 'max:60'],
            'phone'          => ['required', 'string', 'max:40'],
            'zipcode'        => ['nullable', 'string', 'max:10'],
            'address'        => ['required', 'string', 'max:200'],
            'address_detail' => ['nullable', 'string', 'max:200'],
            'memo'           => ['nullable', 'string', 'max:200'],
            'method'         => ['required', 'in:card,vbank,kakao,naver'],
            'user_coupon_id' => ['nullable', 'exists:user_coupons,id'],
            'point_used'     => ['nullable', 'integer', 'min:0'],
        ]);

        $user = Auth::guard('web')->user();
        $customerId = $user->id;
        $items = CartItem::with('product')->where('customer_id', $customerId)->get()
            ->filter(fn ($i) => $i->product);
        abort_if($items->isEmpty(), 400, '장바구니가 비어 있습니다.');

        $subtotal = $items->sum('line_total');

        // 쿠폰 할인
        $couponDiscount = 0;
        $userCoupon = null;
        if (! empty($data['user_coupon_id'])) {
            $userCoupon = UserCoupon::with('coupon')->where('user_id', $customerId)->usable()->find($data['user_coupon_id']);
            if ($userCoupon && $userCoupon->coupon && $userCoupon->coupon->isValid()) {
                $couponDiscount = $userCoupon->coupon->discountFor($subtotal);
            } else {
                $userCoupon = null;
            }
        }

        // 포인트 사용 (보유·잔여금액 이내)
        $pointUsed = min((int) ($data['point_used'] ?? 0), (int) $user->points, max(0, $subtotal - $couponDiscount));
        $total = max(0, $subtotal - $couponDiscount - $pointUsed);
        $pointEarned = (int) floor($total * $user->pointRate());

        $order = DB::transaction(function () use ($data, $user, $customerId, $items, $subtotal, $couponDiscount, $pointUsed, $total, $pointEarned, $userCoupon) {
            $order = Order::create([
                'code'           => 'ORD-' . now()->format('ymd') . '-' . strtoupper(Str::random(4)),
                'customer_id'    => $customerId,
                'receiver_name'  => $data['receiver_name'],
                'phone'          => $data['phone'],
                'zipcode'        => $data['zipcode'] ?? null,
                'address'        => $data['address'],
                'address_detail' => $data['address_detail'] ?? null,
                'memo'           => $data['memo'] ?? null,
                'subtotal'       => $subtotal,
                'shipping_fee'   => 0,
                'discount'       => $couponDiscount,
                'point_used'     => $pointUsed,
                'coupon_id'      => $userCoupon?->coupon_id,
                'point_earned'   => $pointEarned,
                'total'          => $total,
                'status'         => 'pending',
            ]);

            foreach ($items as $it) {
                $orderItem = $order->items()->create([
                    'product_id' => $it->product_id,
                    'brand'      => $it->product->brand,
                    'name'       => $it->product->name,
                    'image'      => $it->product->image,
                    'price'      => $it->product->final_price,
                    'quantity'   => $it->quantity,
                ]);

                // 지점 상품이면 정산 레코드 생성 (본사 수수료 10%)
                if ($it->product->partner_id) {
                    $gross = $it->product->final_price * $it->quantity;
                    $rate = 10;
                    $commission = (int) round($gross * $rate / 100);
                    Settlement::create([
                        'store_id'        => $it->product->partner_id,
                        'order_id'        => $order->id,
                        'order_item_id'   => $orderItem->id,
                        'product_id'      => $it->product_id,
                        'gross_amount'    => $gross,
                        'commission_rate' => $rate,
                        'commission'      => $commission,
                        'net_amount'      => $gross - $commission,
                        'status'          => 'pending',
                    ]);
                }
            }

            // PG 결제 시뮬레이션 — 승인 성공
            Payment::create([
                'order_id' => $order->id, 'method' => $data['method'], 'amount' => $total,
                'status' => 'paid', 'pg_tid' => 'PG' . strtoupper(Str::random(12)), 'paid_at' => now(),
            ]);
            $order->update(['status' => 'paid']);

            // 쿠폰 사용 처리
            if ($userCoupon) {
                $userCoupon->update(['used_at' => now(), 'order_id' => $order->id]);
            }

            // 포인트 사용 차감
            if ($pointUsed > 0) {
                $user->points -= $pointUsed;
                PointTransaction::create(['user_id' => $customerId, 'amount' => -$pointUsed,
                    'reason' => "주문 사용 ({$order->code})", 'order_id' => $order->id, 'balance' => $user->points]);
            }

            // 포인트 적립 + 누적구매 + 등급 재산정
            $user->points += $pointEarned;
            $user->total_spent += $total;
            $user->save();
            if ($pointEarned > 0) {
                PointTransaction::create(['user_id' => $customerId, 'amount' => $pointEarned,
                    'reason' => "구매 적립 ({$order->code})", 'order_id' => $order->id, 'balance' => $user->points]);
            }
            $user->recalcGrade();

            CartItem::where('customer_id', $customerId)->delete();

            return $order;
        });

        // 알림 — 고객 + 본사
        NotificationService::notify('customer', $customerId, 'payment', '💳 결제 완료',
            "주문 {$order->code} · {$this->won($order->total)} 결제가 완료되었습니다.", route('orders.show', $order),
            ['in_app', 'email', 'kakao'], '💳');
        foreach (\App\Models\Admin::pluck('id') as $adminId) {
            NotificationService::notify('admin', $adminId, 'order', '🧾 신규 주문',
                "주문 {$order->code} · {$this->won($order->total)}", route('admin.orders.show', $order), ['in_app'], '🧾');
        }

        return redirect()->route('orders.show', $order)->with('status', '결제가 완료되었습니다!');
    }

    public function index()
    {
        $orders = Order::with('items', 'payment')
            ->where('customer_id', Auth::guard('web')->id())->latest()->paginate(10);

        return view('customer.order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->customer_id === Auth::guard('web')->id(), 403);
        $order->load('items', 'payment');

        return view('customer.order.show', compact('order'));
    }

    private function won(int $n): string
    {
        return number_format($n) . '원';
    }
}
