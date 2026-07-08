<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with('product.category')
            ->where('user_id', Auth::guard('web')->id())->latest()->get()
            ->filter(fn ($w) => $w->product);

        return view('customer.wishlist.index', compact('items'));
    }

    /** 하트 토글 (AJAX) */
    public function toggle(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id']]);
        $userId = Auth::guard('web')->id();

        $existing = Wishlist::where('user_id', $userId)->where('product_id', $data['product_id'])->first();
        if ($existing) {
            $existing->delete();
            $wished = false;
        } else {
            Wishlist::create(['user_id' => $userId, 'product_id' => $data['product_id']]);
            $wished = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json(['wished' => $wished, 'count' => $count]);
    }

    public function remove(Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === Auth::guard('web')->id(), 403);
        $wishlist->delete();

        return back();
    }
}
