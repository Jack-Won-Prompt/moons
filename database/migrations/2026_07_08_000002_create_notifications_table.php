<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('recipient_role', ['customer', 'store', 'admin']);
            $table->unsignedBigInteger('recipient_id');
            $table->string('type')->default('info');   // chat / quote / auction / order / payment / certificate
            $table->string('icon')->default('🔔');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('link')->nullable();
            $table->json('channels')->nullable();       // 발송된 채널: in_app/email/sms/kakao/push
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_role', 'recipient_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
