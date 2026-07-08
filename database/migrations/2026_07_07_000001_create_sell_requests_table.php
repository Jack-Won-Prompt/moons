<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                    // 접수번호 (SR-XXXX)
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();

            // 고객이 본사(head_office) 또는 특정 지점(store)에 직접 판매
            $table->enum('target_type', ['head_office', 'store'])->default('head_office');
            $table->foreignId('target_store_id')->nullable()->constrained('partners')->nullOnDelete();

            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('brand');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('photos')->nullable();                  // 여러 장 사진 경로

            $table->enum('method', ['quote', 'auction'])->default('quote');        // 일반견적 / 경매견적
            $table->enum('delivery_method', ['visit', 'parcel'])->default('parcel'); // 방문 / 택배
            $table->dateTime('visit_at')->nullable();            // 방문 예약
            $table->unsignedBigInteger('desired_price')->nullable();

            // 워크플로우 상태
            $table->string('status')->default('received');
            // received 접수 / appraising 감정중 / photo_requested 사진보완요청 /
            // quoting 견적진행 / auctioning 경매진행 / quoted 견적완료 /
            // customer_approved 고객승인 / inbound 입고 / settled 정산완료 / rejected 반려

            // 감정 결과 (정품 감정 체크리스트)
            $table->json('appraisal')->nullable();
            $table->enum('appraisal_result', ['pending', 'authentic', 'fake', 'uncertain'])->default('pending');
            $table->string('appraiser')->nullable();

            $table->unsignedBigInteger('quote_price')->nullable();       // 최종 견적/매입가
            $table->foreignId('winning_store_id')->nullable()->constrained('partners')->nullOnDelete(); // 경매 낙찰 지점
            $table->text('memo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_requests');
    }
};
