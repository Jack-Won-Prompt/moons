<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');       // 입점사(파트너) 상호
            $table->string('name');               // 담당자명
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('brand')->nullable();  // 대표 취급 브랜드
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('approved');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
