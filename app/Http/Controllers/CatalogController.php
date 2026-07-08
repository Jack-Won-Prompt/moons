<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /** Product list for a category (includes descendant categories). */
    public function category(Request $request, Category $category)
    {
        $childIds = $category->children()->pluck('id')->push($category->id);

        $query = Product::active()->whereIn('category_id', $childIds)->with('category');

        // Sorting — trenbe-style controls
        switch ($request->get('sort')) {
            case 'price_low':  $query->orderByRaw('COALESCE(sale_price, price) ASC'); break;
            case 'price_high': $query->orderByRaw('COALESCE(sale_price, price) DESC'); break;
            case 'discount':   $query->orderByRaw('(price - COALESCE(sale_price, price)) / price DESC'); break;
            case 'popular':    $query->orderByDesc('view_count'); break;
            default:           $query->latest(); break;
        }

        // Optional brand filter
        if ($brand = $request->get('brand')) {
            $query->where('brand', $brand);
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Product::active()->whereIn('category_id', $childIds)
            ->distinct()->orderBy('brand')->pluck('brand');

        return view('storefront.category', compact('category', 'products', 'brands'));
    }

    /** Full catalog / search across all categories. */
    public function all(Request $request)
    {
        $query = Product::active()->with('category');

        if ($keyword = $request->get('q')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('brand', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        switch ($request->get('sort')) {
            case 'price_low':  $query->orderByRaw('COALESCE(sale_price, price) ASC'); break;
            case 'price_high': $query->orderByRaw('COALESCE(sale_price, price) DESC'); break;
            case 'discount':   $query->orderByRaw('(price - COALESCE(sale_price, price)) / price DESC'); break;
            case 'popular':    $query->orderByDesc('view_count'); break;
            default:           $query->latest(); break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.all', compact('products', 'keyword'));
    }

    public function product(Product $product)
    {
        // 판매중지(비활성) 상품은 노출하지 않음
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        $product->load(['reviews.user']);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        $userId = auth('web')->id();
        $wished = $product->wishedBy($userId);

        // 구매 이력이 있고 아직 리뷰를 안 쓴 경우에만 작성 가능
        $canReview = false;
        if ($userId) {
            $bought = \App\Models\OrderItem::where('product_id', $product->id)
                ->whereHas('order', fn ($q) => $q->where('customer_id', $userId)->whereIn('status', ['delivered', 'shipping', 'paid', 'preparing']))
                ->exists();
            $already = $product->reviews()->where('user_id', $userId)->exists();
            $canReview = $bought && ! $already;
        }

        return view('storefront.product', compact('product', 'related', 'wished', 'canReview'));
    }
}
