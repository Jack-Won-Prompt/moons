<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
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

        return view('customer.order.checkout', [
            'items'    => $items,
            'subtotal' => $items->sum('line_total'),
            'user'     => Auth::guard('web')->user(),
        ]);
    }

    /** 주문 생성 + 결제(PG 시뮬레이션) */
    public function place(Request $request)
    {
        $data = $request->validate([
            'receiver_name' => ['required', 'string', 'max:60'],
            'phone'         => ['required', 'string', 'max:40'],
            'zipcode'       => ['nullable', 'string', 'max:10'],
            'address'       => ['required', 'string', 'max:200'],
            'address_detail'=> ['nullable', 'string', 'max:200'],
            'memo'          => ['nullable', 'string', 'max:200'],
            'method'        => ['required', 'in:card,vbank,kakao,naver'],
        ]);

        $customerId = Auth::guard('web')->id();
        $items = CartItem::with('product')->where('customer_id', $customerId)->get()
            ->filter(fn ($i) => $i->product);
        abort_if($items->isEmpty(), 400, '장바구니가 비어 있습니다.');

        $subtotal = $items->sum('line_total');
        $shipping = 0;

        $order = DB::transaction(function () use ($data, $customerId, $items, $subtotal, $shipping) {
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
                'shipping_fee'   => $shipping,
                'total'          => $subtotal + $shipping,
                'status'         => 'pending',
            ]);

            foreach ($items as $it) {
                $order->items()->create([
                    'product_id' => $it->product_id,
                    'brand'      => $it->product->brand,
                    'name'       => $it->product->name,
                    'image'      => $it->product->image,
                    'price'      => $it->product->final_price,
                    'quantity'   => $it->quantity,
                ]);
            }

            // PG 결제 시뮬레이션 — 승인 성공 처리
            Payment::create([
                'order_id' => $order->id,
                'method'   => $data['method'],
                'amount'   => $order->total,
                'status'   => 'paid',
                'pg_tid'   => 'PG' . strtoupper(Str::random(12)),
                'paid_at'  => now(),
            ]);
            $order->update(['status' => 'paid']);

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
