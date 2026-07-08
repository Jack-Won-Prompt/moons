<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Settlement;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index()
    {
        $byStore = Settlement::selectRaw('store_id,
                sum(gross_amount) gross, sum(commission) commission, sum(net_amount) net,
                sum(case when status="pending" then net_amount else 0 end) pending')
            ->with('store')->groupBy('store_id')->get();

        $settlements = Settlement::with('store', 'order', 'product')->latest()->paginate(15);

        $stats = [
            'gross'      => (int) Settlement::sum('gross_amount'),
            'commission' => (int) Settlement::sum('commission'),
            'pending'    => (int) Settlement::where('status', 'pending')->sum('net_amount'),
            'paid'       => (int) Settlement::where('status', 'paid')->sum('net_amount'),
        ];

        return view('admin.settlements.index', compact('byStore', 'settlements', 'stats'));
    }

    /** 지점 정산 지급 (미지급 건 일괄 처리) */
    public function payStore(Request $request, Partner $partner)
    {
        $count = Settlement::where('store_id', $partner->id)->where('status', 'pending')->count();
        $amount = (int) Settlement::where('store_id', $partner->id)->where('status', 'pending')->sum('net_amount');

        Settlement::where('store_id', $partner->id)->where('status', 'pending')
            ->update(['status' => 'paid', 'paid_at' => now()]);

        NotificationService::notify('store', $partner->id, 'settlement', '💵 정산 완료',
            "{$count}건 · " . number_format($amount) . '원이 정산되었습니다.', route('partner.settlements.index'), ['in_app', 'email'], '💵');

        return back()->with('status', "{$partner->company_name} 정산 {$count}건(" . number_format($amount) . '원) 지급 완료');
    }
}
