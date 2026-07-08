<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('product.category')
            ->where('customer_id', Auth::guard('web')->id())->get()
            ->filter(fn ($i) => $i->product);

        $subtotal = $items->sum('line_total');

        return view('customer.cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:10'],
            'buy_now'    => ['nullable', 'boolean'],
        ]);

        $item = CartItem::firstOrNew([
            'customer_id' => Auth::guard('web')->id(),
            'product_id'  => $data['product_id'],
        ]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + ($data['quantity'] ?? 1);
        $item->save();

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout');
        }

        return back()->with('status', '장바구니에 담았습니다.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->customer_id === Auth::guard('web')->id(), 403);
        $cartItem->update($request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:10']]));

        return back();
    }

    public function remove(CartItem $cartItem)
    {
        abort_unless($cartItem->customer_id === Auth::guard('web')->id(), 403);
        $cartItem->delete();

        return back()->with('status', '상품을 삭제했습니다.');
    }
}
