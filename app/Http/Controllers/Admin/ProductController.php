<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'partner'])
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%")
                ->orWhere('brand', 'like', "%{$request->q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product'    => new Product(),
            'categories' => Category::orderBy('sort')->orderBy('name')->get(),
            'partners'   => Partner::orderBy('company_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['brand'] . '-' . $data['name']) . '-' . Str::random(5);
        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', '상품이 등록되었습니다.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product'    => $product,
            'categories' => Category::orderBy('sort')->orderBy('name')->get(),
            'partners'   => Partner::orderBy('company_name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', '상품이 수정되었습니다.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('status', '상품이 삭제되었습니다.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'partner_id'  => ['nullable', 'exists:partners,id'],
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
