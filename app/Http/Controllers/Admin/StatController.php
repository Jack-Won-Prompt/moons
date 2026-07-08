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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public function index()
    {
        $paidStatuses = ['paid', 'preparing', 'shipping', 'delivered'];

        $kpi = [
            'revenue'   => (int) Order::whereIn('status', $paidStatuses)->sum('total'),
            'orders'    => Order::whereIn('status', $paidStatuses)->count(),
            'members'   => User::count(),
            'appraisals'=> SellRequest::where('appraisal_result', '!=', 'pending')->count(),
            'certs'     => Certificate::count(),
            'inventory' => (int) Inventory::sum('quantity'),
        ];
        $kpi['avg_order'] = $kpi['orders'] ? (int) round($kpi['revenue'] / $kpi['orders']) : 0;

        // 최근 7일 매출 (일별)
        $days = collect(range(6, 0))->map(function ($d) use ($paidStatuses) {
            $date = Carbon::today()->subDays($d);
            $sum = Order::whereIn('status', $paidStatuses)->whereDate('created_at', $date)->sum('total');
            return ['label' => $date->format('m/d'), 'value' => (int) $sum];
        });

        // 감정 결과 분포
        $appraisal = SellRequest::selectRaw('appraisal_result r, count(*) c')->groupBy('appraisal_result')->pluck('c', 'r');

        // 회원 등급 분포
        $grades = User::selectRaw('grade, count(*) c')->groupBy('grade')->pluck('c', 'grade');

        // 브랜드 TOP (카탈로그 상품 수)
        $topBrands = Product::selectRaw('brand, count(*) c')->groupBy('brand')->orderByDesc('c')->limit(8)->get();

        // 이동 현황
        $transfers = StockTransfer::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('admin.stats.index', compact('kpi', 'days', 'appraisal', 'grades', 'topBrands', 'transfers'));
    }
}
