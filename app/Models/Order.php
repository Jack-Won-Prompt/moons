<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code', 'customer_id', 'receiver_name', 'phone', 'zipcode', 'address',
        'address_detail', 'memo', 'subtotal', 'shipping_fee', 'total', 'status', 'tracking_no',
    ];

    public const STATUSES = [
        'pending'   => ['결제 대기', 'gray'],
        'paid'      => ['결제 완료', 'green'],
        'preparing' => ['상품 준비', 'amber'],
        'shipping'  => ['배송 중', 'amber'],
        'delivered' => ['배송 완료', 'green'],
        'cancelled' => ['취소', 'red'],
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status][0] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status][1] ?? 'gray';
    }
}
