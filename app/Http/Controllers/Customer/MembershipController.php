<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $user->load(['coupons.coupon', 'pointTransactions']);

        // 받을 수 있는 쿠폰 (활성 · 미보유)
        $ownedIds = $user->coupons->pluck('coupon_id')->all();
        $claimable = Coupon::where('is_active', true)->whereNotIn('id', $ownedIds)->get()
            ->filter(fn ($c) => $c->isValid());

        return view('customer.membership.index', compact('user', 'claimable'));
    }

    public function claim(Coupon $coupon)
    {
        $user = Auth::guard('web')->user();
        abort_unless($coupon->isValid(), 400, '사용할 수 없는 쿠폰입니다.');

        UserCoupon::firstOrCreate(['user_id' => $user->id, 'coupon_id' => $coupon->id]);

        return back()->with('status', "'{$coupon->name}' 쿠폰을 받았습니다.");
    }
}
