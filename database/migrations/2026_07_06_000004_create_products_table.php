<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('brand');                       // 브랜드명 (트렌비 스타일 카드 상단)
            $table->string('name');                        // 상품명
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price');              // 정가
            $table->unsignedInteger('sale_price')->nullable(); // 판매가(할인가)
            $table->string('image')->nullable();           // 대표 이미지 (gradient placeholder key)
            $table->string('color')->nullable();           // placeholder gradient seed
            $table->unsignedInteger('stock')->default(999);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_best')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
