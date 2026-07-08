<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_request_id')->constrained('sell_requests')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('partners')->cascadeOnDelete();
            $table->unsignedBigInteger('bid_price');
            $table->text('message')->nullable();
            $table->enum('status', ['active', 'won', 'lost', 'cancelled'])->default('active');
            $table->timestamps();

            $table->unique(['sell_request_id', 'store_id']); // 지점당 1입찰(수정 가능)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
    }
};
