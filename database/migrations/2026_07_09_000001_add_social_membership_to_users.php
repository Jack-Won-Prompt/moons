<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // SNS 로그인
            $table->string('provider')->nullable()->after('email');       // kakao/naver/google
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('avatar')->nullable()->after('provider_id');
            // 멤버십
            $table->string('grade')->default('bronze')->after('avatar');   // bronze/silver/gold/vip
            $table->unsignedBigInteger('points')->default(0)->after('grade');
            $table->unsignedBigInteger('total_spent')->default(0)->after('points');
            // 비밀번호는 SNS 가입 시 nullable
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'avatar', 'grade', 'points', 'total_spent']);
        });
    }
};
