<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $newArrivals = Product::active()->where('is_new', true)->latest()->take(8)->get();
        $best        = Product::active()->where('is_best', true)->take(8)->get();
        $sale        = Product::active()->whereNotNull('sale_price')
            ->orderByRaw('(price - sale_price) / price DESC')->take(8)->get();

        $rootCategories = Category::roots()->where('is_active', true)->get();

        return view('storefront.home', compact('newArrivals', 'best', 'sale', 'rootCategories'));
    }
}
