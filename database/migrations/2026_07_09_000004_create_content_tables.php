<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('eyebrow')->nullable();
            $table->string('image')->nullable();
            $table->string('gradient')->nullable();     // 이미지 없을 때 배경
            $table->string('link')->nullable();
            $table->enum('position', ['hero', 'strip'])->default('hero');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('gradient')->nullable();
            $table->json('filters')->nullable();        // 예: {brand, category_id, min_discount}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['notice', 'event'])->default('notice');
            $table->string('title');
            $table->longText('body')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('일반');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('banners');
    }
};
