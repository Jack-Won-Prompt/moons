<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('image');      // 상세페이지 추가 이미지 URL 목록
            $table->string('source_no')->nullable()->after('gallery'); // 원본(미스터문) product_no
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['gallery', 'source_no']);
        });
    }
};
