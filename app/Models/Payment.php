<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'method', 'amount', 'status', 'pg_tid', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public const METHODS = ['card' => '신용카드', 'vbank' => '가상계좌', 'kakao' => '카카오페이', 'naver' => '네이버페이'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }
}
