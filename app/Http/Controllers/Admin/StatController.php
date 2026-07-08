<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellRequest;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatController extends Controller
{
    private const PAID = ['paid', 'preparing', 'shipping', 'delivered'];

    public function index(Request $request)
    {
        $days = (int) $request->get('days', 7);
        if (! in_array($days, [7, 30, 90])) {
            $days = 7;
        }
        $since = Carbon::today()->subDays($days - 1);

        $kpi = [
            'revenue'   => (int) Order::whereIn('status', self::PAID)->where('created_at', '>=', $since)->sum('total'),
            'orders'    => Order::whereIn('status', self::PAID)->where('created_at', '>=', $since)->count(),
            'members'   => User::where('created_at', '>=', $since)->count(),
            'appraisals'=> SellRequest::where('appraisal_result', '!=', 'pending')->where('created_at', '>=', $since)->count(),
            'certs'     => Certificate::where('created_at', '>=', $since)->count(),
            'inventory' => (int) Inventory::sum('quantity'),
        ];
        $kpi['avg_order'] = $kpi['orders'] ? (int) round($kpi['revenue'] / $kpi['orders']) : 0;

        // 매출 추이 — 30일 이하 일별, 90일은 주별 버킷
        if ($days <= 30) {
            $bars = collect(range($days - 1, 0))->map(function ($d) {
                $date = Carbon::today()->subDays($d);
                return ['label' => $date->format('m/d'),
                    'value' => (int) Order::whereIn('status', self::PAID)->whereDate('created_at', $date)->sum('total')];
            });
        } else {
            $bars = collect(range(12, 0))->map(function ($w) {
                $start = Carbon::today()->subWeeks($w)->startOfWeek();
                $end = (clone $start)->endOfWeek();
                return ['label' => $start->format('m/d'),
                    'value' => (int) Order::whereIn('status', self::PAID)->whereBetween('created_at', [$start, $end])->sum('total')];
            });
        }

        $appraisal = SellRequest::selectRaw('appraisal_result r, count(*) c')->groupBy('appraisal_result')->pluck('c', 'r');
        $grades = User::selectRaw('grade, count(*) c')->groupBy('grade')->pluck('c', 'grade');
        $topBrands = Product::selectRaw('brand, count(*) c')->groupBy('brand')->orderByDesc('c')->limit(8)->get();
        $transfers = StockTransfer::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('admin.stats.index', compact('kpi', 'bars', 'appraisal', 'grades', 'topBrands', 'transfers', 'days'));
    }
}
