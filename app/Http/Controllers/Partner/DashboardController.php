<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $partner = Auth::guard('partner')->user();

        $stats = [
            'products' => Product::where('partner_id', $partner->id)->count(),
            'active'   => Product::where('partner_id', $partner->id)->where('is_active', true)->count(),
            'views'    => (int) Product::where('partner_id', $partner->id)->sum('view_count'),
        ];

        $recent = Product::where('partner_id', $partner->id)->latest()->take(6)->get();

        return view('partner.dashboard', compact('partner', 'stats', 'recent'));
    }
}
