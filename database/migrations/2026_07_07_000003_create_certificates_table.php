<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                 // 감정서 번호 / QR 조회 코드
            $table->foreignId('sell_request_id')->nullable()->constrained('sell_requests')->nullOnDelete();

            $table->string('brand');
            $table->string('model')->nullable();
            $table->string('category')->nullable();
            $table->enum('result', ['authentic', 'fake', 'uncertain'])->default('authentic');
            $table->string('appraiser')->nullable();
            $table->string('issuer')->nullable();             // 발급 지점/본사
            $table->string('thumbnail')->nullable();

            $table->string('blockchain_hash', 64)->nullable(); // sha256 (시뮬레이션)
            $table->json('dpp')->nullable();                   // Digital Product Passport 생애 이력

            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
