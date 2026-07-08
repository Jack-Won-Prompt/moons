<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@moons.com')->first();
        if (! $customer) {
            return;
        }

        // 리뷰 (로컬 이미지 상품에 포토후기)
        $products = Product::where('image', 'like', 'assets/products/%')->inRandomOrder()->take(6)->get();
        $bodies = ['정품 확실하고 상태 최고예요!', '감정서 QR로 검증되니 안심됩니다.', '배송 빠르고 포장 꼼꼼해요.', '가격 대비 만족스러운 명품이에요.', '실물이 더 예뻐요. 재구매 의사 있어요.'];
        foreach ($products as $i => $p) {
            Review::updateOrCreate(
                ['product_id' => $p->id, 'user_id' => $customer->id],
                ['rating' => 4 + ($i % 2), 'body' => $bodies[$i % count($bodies)], 'photos' => [$p->image]]
            );
        }

        // 위시리스트
        foreach (Product::whereNotNull('image')->inRandomOrder()->take(5)->pluck('id') as $pid) {
            Wishlist::firstOrCreate(['user_id' => $customer->id, 'product_id' => $pid]);
        }

        // 지점 상품 주문 → 정산 생성
        $storeProduct = Product::where('slug', 'store-demo-1')->first();
        if ($storeProduct && $storeProduct->partner_id) {
            $order = Order::updateOrCreate(
                ['code' => 'ORD-DEMO-STORE'],
                [
                    'customer_id' => $customer->id, 'receiver_name' => $customer->name, 'phone' => '010-0000-0000',
                    'address' => '서울 강남구', 'subtotal' => $storeProduct->final_price, 'total' => $storeProduct->final_price,
                    'status' => 'delivered',
                ]
            );
            $item = $order->items()->updateOrCreate(
                ['product_id' => $storeProduct->id],
                ['brand' => $storeProduct->brand, 'name' => $storeProduct->name, 'image' => $storeProduct->image,
                 'price' => $storeProduct->final_price, 'quantity' => 1]
            );
            Payment::updateOrCreate(['order_id' => $order->id], ['method' => 'card', 'amount' => $order->total, 'status' => 'paid', 'pg_tid' => 'PG' . strtoupper(Str::random(10)), 'paid_at' => now()]);

            $gross = $storeProduct->final_price;
            $commission = (int) round($gross * 0.10);
            Settlement::updateOrCreate(
                ['order_item_id' => $item->id],
                ['store_id' => $storeProduct->partner_id, 'order_id' => $order->id, 'product_id' => $storeProduct->id,
                 'gross_amount' => $gross, 'commission_rate' => 10, 'commission' => $commission, 'net_amount' => $gross - $commission, 'status' => 'pending']
            );
        }

        $this->command->info('데모: 리뷰 ' . Review::count() . '건, 위시 ' . Wishlist::count() . '건, 정산 ' . Settlement::count() . '건');
    }
}
