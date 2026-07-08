<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanners = Banner::active()->where('position', 'hero')->get();
        $promotions  = Promotion::where('is_active', true)->latest()->take(3)->get();
        $newArrivals = Product::active()->where('is_new', true)->latest()->take(8)->get();
        $best        = Product::active()->where('is_best', true)->take(8)->get();
        $sale        = Product::active()->whereNotNull('sale_price')
            ->orderByRaw('(price - sale_price) / price DESC')->take(8)->get();

        $rootCategories = Category::roots()->where('is_active', true)->get();

        // Hero slider — high-discount products that have an image
        $slides = Product::active()->whereNotNull('image')->whereNotNull('sale_price')
            ->orderByRaw('(price - sale_price) / price DESC')
            ->take(6)->get();

        // Side feature cards (image backgrounds)
        $bagFeature  = Product::active()->whereNotNull('image')
            ->whereRelation('category', 'slug', 'handbags')->where('is_best', true)->first();
        $shoeFeature = Product::active()->whereNotNull('image')
            ->whereRelation('category', 'slug', 'womens-shoes')->where('is_best', true)->first();

        return view('storefront.home', compact(
            'newArrivals', 'best', 'sale', 'rootCategories', 'slides', 'bagFeature', 'shoeFeature',
            'heroBanners', 'promotions'
        ));
    }
}
