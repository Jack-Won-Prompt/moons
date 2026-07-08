<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $userId = Auth::guard('web')->id();

        $bought = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('customer_id', $userId))->exists();
        abort_unless($bought, 403, '구매한 상품만 리뷰를 작성할 수 있습니다.');
        abort_if(Review::where('product_id', $product->id)->where('user_id', $userId)->exists(), 400, '이미 작성한 리뷰가 있습니다.');

        $data = $request->validate([
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'body'     => ['nullable', 'string', 'max:1000'],
            'photos'   => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:8192'],
        ]);

        $paths = [];
        if ($request->hasFile('photos')) {
            $dir = base_path("assets/uploads/reviews/{$product->id}");
            @mkdir($dir, 0777, true);
            foreach ($request->file('photos') as $file) {
                $name = Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $paths[] = "assets/uploads/reviews/{$product->id}/{$name}";
            }
        }

        Review::create([
            'product_id' => $product->id, 'user_id' => $userId,
            'rating' => $data['rating'], 'body' => $data['body'] ?? null, 'photos' => $paths,
        ]);

        return back()->with('status', '소중한 후기 감사합니다!');
    }

    /** 공개 포토후기 갤러리 */
    public function gallery()
    {
        $reviews = Review::withPhotos()->with(['product', 'user'])->latest()->paginate(24);

        return view('content.reviews', compact('reviews'));
    }
}
