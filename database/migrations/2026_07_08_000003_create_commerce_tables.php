<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['customer_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->string('receiver_name');
            $table->string('phone');
            $table->string('zipcode')->nullable();
            $table->string('address');
            $table->string('address_detail')->nullable();
            $table->string('memo')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('status')->default('pending');
            // pending 결제대기 / paid 결제완료 / preparing 상품준비 / shipping 배송중 / delivered 배송완료 / cancelled 취소
            $table->string('tracking_no')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('brand');
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('method', ['card', 'vbank', 'kakao', 'naver'])->default('card');
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['ready', 'paid', 'failed', 'cancelled'])->default('ready');
            $table->string('pg_tid')->nullable();   // 시뮬레이션 거래번호
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
    }
};
