<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('partner_id', Auth::guard('partner')->id())
            ->latest()->paginate(15);

        return view('partner.products.index', compact('products'));
    }

    public function create()
    {
        return view('partner.products.form', [
            'product'    => new Product(),
            'categories' => Category::orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['partner_id'] = Auth::guard('partner')->id();
        $data['slug'] = Str::slug($data['brand'] . '-' . $data['name']) . '-' . Str::random(5);
        Product::create($data);

        return redirect()->route('partner.products.index')->with('status', '상품이 등록되었습니다.');
    }

    public function edit(Product $product)
    {
        $this->authorizeOwner($product);

        return view('partner.products.form', [
            'product'    => $product,
            'categories' => Category::orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeOwner($product);
        $product->update($this->validated($request));

        return redirect()->route('partner.products.index')->with('status', '상품이 수정되었습니다.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeOwner($product);
        $product->delete();

        return back()->with('status', '상품이 삭제되었습니다.');
    }

    private function authorizeOwner(Product $product): void
    {
        abort_unless($product->partner_id === Auth::guard('partner')->id(), 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand'       => ['required', 'string', 'max:255'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'integer', 'min:0'],
            'sale_price'  => ['nullable', 'integer', 'min:0'],
            'color'       => ['nullable', 'string', 'max:60'],
            'stock'       => ['required', 'integer', 'min:0'],
            'is_new'      => ['nullable', 'boolean'],
            'is_best'     => ['nullable', 'boolean'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
    }
}
