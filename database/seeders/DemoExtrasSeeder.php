<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Inventory;
use App\Models\Notice;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $partners = \App\Models\Partner::orderBy('id')->take(2)->get();
        if ($partners->count() < 2) {
            $this->command->warn('파트너(지점)가 필요합니다.');
            return;
        }
        [$storeA, $storeB] = [$partners[0], $partners[1]];
        $customer = User::where('email', 'customer@moons.com')->first();

        /* 멤버십: 고객 포인트/등급 */
        if ($customer) {
            $customer->update(['points' => 50000, 'total_spent' => 3200000]);
            $customer->recalcGrade();
            PointTransaction::firstOrCreate(
                ['user_id' => $customer->id, 'reason' => '가입 웰컴 적립'],
                ['amount' => 50000, 'balance' => 50000]
            );
        }

        /* 쿠폰 */
        $c1 = Coupon::updateOrCreate(['code' => 'CP-WELCOME'], [
            'name' => '신규가입 15만원 할인', 'type' => 'fixed', 'value' => 150000, 'min_order' => 1000000, 'is_active' => true,
        ]);
        $c2 = Coupon::updateOrCreate(['code' => 'CP-VIP10'], [
            'name' => 'VIP 10% 할인', 'type' => 'percent', 'value' => 10, 'min_order' => 500000, 'max_discount' => 500000, 'is_active' => true,
        ]);
        if ($customer) {
            foreach ([$c1, $c2] as $c) {
                UserCoupon::firstOrCreate(['user_id' => $customer->id, 'coupon_id' => $c->id]);
            }
        }

        /* 멀티지점 재고: 인기 상품 일부를 두 지점에 배치 */
        $products = Product::whereNotNull('image')->inRandomOrder()->take(12)->get();
        foreach ($products as $i => $p) {
            $store = $i % 2 === 0 ? $storeA : $storeB;
            Inventory::updateOrCreate(
                ['store_id' => $store->id, 'product_id' => $p->id],
                ['quantity' => 2 + ($i % 5), 'location' => chr(65 + $i % 4) . '-' . (($i % 3) + 1) . '-' . (($i % 5) + 1)]
            );
        }
        // 이동 요청 예시
        $shared = $products->first();
        if ($shared) {
            StockTransfer::updateOrCreate(['code' => 'TR-DEMO1'], [
                'product_id' => $shared->id, 'from_store_id' => $storeA->id, 'to_store_id' => $storeB->id,
                'quantity' => 1, 'status' => 'requested', 'reason' => '고객 방문 예약', 'customer_wish' => true,
            ]);
        }

        /* 지점 소유 상품 (본사/지점 구분 + 판매노출 데모) */
        Product::updateOrCreate(['slug' => 'store-demo-1'], [
            'category_id' => \App\Models\Category::where('slug', 'handbags')->value('id') ?? Product::value('category_id'),
            'partner_id'  => $storeA->id,
            'brand'       => 'CHANEL', 'name' => '[' . $storeA->company_name . ' 단독] 클래식 미니 플랩백',
            'description' => '지점 직매입 정품 상품', 'price' => 8500000, 'sale_price' => 7900000,
            'image'       => Product::whereNotNull('image')->value('image'),
            'stock'       => 3, 'is_new' => true, 'is_best' => true, 'is_active' => true,
        ]);

        /* 배너 (대표 상품 이미지로 채움) */
        $bagImg  = Product::whereRelation('category', 'slug', 'handbags')->where('image', 'like', 'assets/products/%')->inRandomOrder()->value('image');
        $watchImg = Product::whereRelation('category', 'slug', 'watches')->where('image', 'like', 'assets/products/%')->inRandomOrder()->value('image');
        Banner::updateOrCreate(['title' => '단독 특가 위크'], [
            'eyebrow' => 'MOONS ONLY', 'subtitle' => '이번 주만 만나는 단독 할인', 'gradient' => '#1a1a2e,#4b1248',
            'image' => $bagImg, 'link' => '/products?sort=discount', 'position' => 'hero', 'sort' => 0, 'is_active' => true,
        ]);
        Banner::updateOrCreate(['title' => '럭셔리 워치 컬렉션'], [
            'eyebrow' => 'TIMELESS', 'subtitle' => '정품 감정 완료 명품 시계', 'gradient' => '#0f3443,#203a43',
            'image' => $watchImg, 'link' => '/category/watches', 'position' => 'hero', 'sort' => 1, 'is_active' => true,
        ]);
        Banner::updateOrCreate(['title' => '신규회원 15만원 쿠폰'], [
            'eyebrow' => 'WELCOME', 'subtitle' => '지금 가입하고 혜택 받기', 'gradient' => '#0f3443,#34e89e',
            'image' => Product::whereRelation('category', 'slug', 'accessories')->where('image', 'like', 'assets/products/%')->inRandomOrder()->value('image'),
            'link' => '/register', 'position' => 'hero', 'sort' => 2, 'is_active' => true,
        ]);

        /* 기획전 */
        Promotion::updateOrCreate(['code' => 'PR-GUCCI'], [
            'title' => 'GUCCI 컬렉션', 'subtitle' => '구찌 인기 상품 모음', 'gradient' => '#603813,#b29f94',
            'filters' => ['brand' => 'GUCCI'], 'is_active' => true,
        ]);
        Promotion::updateOrCreate(['code' => 'PR-SALE50'], [
            'title' => '50% 이상 특가', 'subtitle' => '반값 이하 명품', 'gradient' => '#8e0e00,#1f1c18',
            'filters' => ['min_discount' => 50], 'is_active' => true,
        ]);
        Promotion::updateOrCreate(['code' => 'PR-LV'], [
            'title' => 'LOUIS VUITTON', 'subtitle' => '루이비통 스페셜 셀렉션', 'gradient' => '#2c1810,#5c2e0e',
            'filters' => ['brand' => 'LOUIS VUITTON'], 'is_active' => true,
        ]);

        /* 공지 · FAQ */
        Notice::updateOrCreate(['title' => 'MOONS 정품 감정 시스템 오픈'], ['category' => 'notice', 'body' => "블록체인 기반 감정서와 Digital Product Passport를 도입했습니다.\n모든 상품은 정품 감정 후 판매됩니다.", 'is_pinned' => true]);
        Notice::updateOrCreate(['title' => '여름 시즌 특가 이벤트'], ['category' => 'event', 'body' => "7월 한 달간 인기 명품을 특가로 만나보세요."]);

        $faqs = [
            ['결제', '어떤 결제수단을 지원하나요?', '신용카드, 카카오페이, 네이버페이, 가상계좌를 지원합니다.'],
            ['배송', '배송비는 얼마인가요?', '모든 상품은 무료배송이며 정품 보증서가 동봉됩니다.'],
            ['감정', '정품 감정은 어떻게 확인하나요?', '상품 수령 시 동봉된 감정서의 QR을 스캔하면 블록체인 검증 결과와 DPP를 확인할 수 있습니다.'],
            ['판매', '내 명품을 팔 수 있나요?', '판매하기 메뉴에서 본사 또는 지점에 직접 판매(위탁/경매)를 신청할 수 있습니다.'],
        ];
        foreach ($faqs as $i => [$cat, $q, $a]) {
            Faq::updateOrCreate(['question' => $q], ['category' => $cat, 'answer' => $a, 'sort' => $i]);
        }

        $this->command->info('데모 확장 데이터 생성 완료 (재고·쿠폰·배너·기획전·공지·FAQ·지점상품).');
    }
}
