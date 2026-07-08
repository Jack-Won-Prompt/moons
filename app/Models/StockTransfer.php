<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'code', 'product_id', 'from_store_id', 'to_store_id', 'quantity',
        'status', 'customer_wish', 'reason',
    ];

    protected $casts = ['customer_wish' => 'boolean'];

    public const STATUSES = [
        'requested' => ['이동 요청', 'amber'],
        'approved'  => ['승인됨', 'green'],
        'shipping'  => ['이동중', 'amber'],
        'completed' => ['이동 완료', 'green'],
        'rejected'  => ['반려', 'red'],
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromStore()
    {
        return $this->belongsTo(Partner::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Partner::class, 'to_store_id');
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
