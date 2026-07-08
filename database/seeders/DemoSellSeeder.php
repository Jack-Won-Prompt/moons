<?php

namespace Database\Seeders;

use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Partner;
use App\Models\SellRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSellSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@moons.com')->first();
        $stores   = Partner::orderBy('id')->take(2)->get();
        if (! $customer || $stores->count() < 1) {
            $this->command->warn('데모 계정이 없습니다. 먼저 db:seed 를 실행하세요.');
            return;
        }
        $storeA = $stores[0];
        $storeB = $stores[1] ?? $stores[0];
        $cat = Category::first();

        SellRequest::where('code', 'like', 'SR-DEMO%')->delete();

        // 1) 접수 (본사, 일반견적)
        SellRequest::create([
            'code' => 'SR-DEMO-1', 'customer_id' => $customer->id, 'target_type' => 'head_office',
            'category_id' => $cat?->id, 'brand' => 'CHANEL', 'title' => '클래식 미디움 플랩백 캐비어',
            'description' => '3년 사용, 정품 카드·더스트백 보유', 'method' => 'quote', 'delivery_method' => 'parcel',
            'desired_price' => 6500000, 'status' => 'received',
        ]);

        // 2) 견적완료 (지점, 승인 대기)
        SellRequest::create([
            'code' => 'SR-DEMO-2', 'customer_id' => $customer->id, 'target_type' => 'store', 'target_store_id' => $storeA->id,
            'category_id' => $cat?->id, 'brand' => 'HERMES', 'title' => '버킨 30 토고 골드',
            'method' => 'quote', 'delivery_method' => 'visit', 'desired_price' => 18000000,
            'appraisal' => ['brand' => 'ok', 'serial' => 'ok', 'logo' => 'ok', 'leather' => 'ok'],
            'appraisal_result' => 'authentic', 'appraiser' => '김감정 (' . $storeA->company_name . ')',
            'quote_price' => 17500000, 'status' => 'quoted',
        ]);

        // 3) 경매진행 (2개 지점 입찰)
        $auction = SellRequest::create([
            'code' => 'SR-DEMO-3', 'customer_id' => $customer->id, 'target_type' => 'head_office',
            'category_id' => $cat?->id, 'brand' => 'ROLEX', 'title' => '데이저스트 41 오이스터 블루',
            'method' => 'auction', 'delivery_method' => 'parcel', 'desired_price' => 12000000, 'status' => 'auctioning',
        ]);
        AuctionBid::create(['sell_request_id' => $auction->id, 'store_id' => $storeA->id, 'bid_price' => 11800000, 'message' => '당일 정산 가능합니다']);
        AuctionBid::create(['sell_request_id' => $auction->id, 'store_id' => $storeB->id, 'bid_price' => 12200000, 'message' => '최고가 제시']);

        // 4) 정산완료 + 감정서/DPP 발급
        $settled = SellRequest::create([
            'code' => 'SR-DEMO-4', 'customer_id' => $customer->id, 'target_type' => 'store', 'target_store_id' => $storeA->id,
            'category_id' => $cat?->id, 'brand' => 'LOUIS VUITTON', 'title' => '카퓌신 MM 토뤼용 블랙',
            'method' => 'quote', 'delivery_method' => 'parcel',
            'appraisal' => ['brand' => 'ok', 'model' => 'ok', 'serial' => 'ok', 'logo' => 'ok', 'stitching' => 'ok', 'leather' => 'ok', 'metal' => 'ok', 'components' => 'ok'],
            'appraisal_result' => 'authentic', 'appraiser' => '이감정 (' . $storeA->company_name . ')',
            'quote_price' => 4200000, 'winning_store_id' => $storeA->id, 'status' => 'settled',
        ]);
        $now = now();
        $cert = new Certificate([
            'code' => 'MOONS-2026-' . strtoupper(Str::random(6)),
            'sell_request_id' => $settled->id, 'brand' => 'LOUIS VUITTON', 'model' => '카퓌신 MM 토뤼용 블랙',
            'category' => $cat?->name, 'result' => 'authentic', 'appraiser' => $settled->appraiser,
            'issuer' => $storeA->company_name, 'issued_at' => $now,
            'dpp' => [
                ['type' => 'appraisal', 'at' => $now->toDateTimeString(), 'by' => $settled->appraiser, 'note' => '정품 감정 완료 (8항목 정상)'],
                ['type' => 'ownership', 'at' => $now->toDateTimeString(), 'by' => 'MOONS', 'note' => '소유권 이전: 고객 → ' . $storeA->company_name],
                ['type' => 'storage', 'at' => $now->toDateTimeString(), 'by' => $storeA->company_name, 'note' => '지점 입고·보관'],
            ],
        ]);
        $cert->blockchain_hash = $cert->computeHash();
        $cert->save();

        $this->command->info('데모 판매접수 4건 + 감정서 1건 생성 완료. 감정서: ' . $cert->code);
    }
}
