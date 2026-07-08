<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index()
    {
        $gradeDist = User::selectRaw('grade, count(*) c, sum(points) pts')->groupBy('grade')->get()->keyBy('grade');
        $coupons = Coupon::withCount('userCoupons')->latest()->get();
        $stats = [
            'members'      => User::count(),
            'points_total' => (int) User::sum('points'),
            'points_issued'=> (int) PointTransaction::where('amount', '>', 0)->sum('amount'),
            'coupons'      => Coupon::count(),
        ];

        return view('admin.membership.index', compact('gradeDist', 'coupons', 'stats'));
    }

    public function storeCoupon(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'type'         => ['required', 'in:fixed,percent'],
            'value'        => ['required', 'integer', 'min:1'],
            'min_order'    => ['nullable', 'integer', 'min:0'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'expires_at'   => ['nullable', 'date'],
            'issue_all'    => ['nullable', 'boolean'],
        ]);
        $data['code'] = 'CP-' . strtoupper(Str::random(6));
        $data['min_order'] = $data['min_order'] ?? 0;
        $coupon = Coupon::create($data);

        // 전체 회원에게 발급
        if ($request->boolean('issue_all')) {
            foreach (User::pluck('id') as $uid) {
                UserCoupon::firstOrCreate(['user_id' => $uid, 'coupon_id' => $coupon->id]);
                NotificationService::notify('customer', $uid, 'coupon', '🎟️ 쿠폰 도착',
                    "{$coupon->name} ({$coupon->label})", route('membership.index'), ['in_app', 'kakao'], '🎟️');
            }
        }

        return back()->with('status', "쿠폰 '{$coupon->name}'을(를) 생성했습니다.");
    }

    public function toggleCoupon(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back();
    }
}
