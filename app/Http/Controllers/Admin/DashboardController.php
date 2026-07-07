<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products'   => Product::count(),
            'categories' => Category::count(),
            'partners'   => Partner::count(),
            'customers'  => User::count(),
            'pending'    => Partner::where('status', 'pending')->count(),
        ];

        $recentProducts = Product::with('partner')->latest()->take(6)->get();
        $pendingPartners = Partner::where('status', 'pending')->latest()->get();

        return view('admin.dashboard', compact('stats', 'recentProducts', 'pendingPartners'));
    }
}
