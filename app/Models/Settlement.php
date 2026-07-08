<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = [
        'store_id', 'order_id', 'order_item_id', 'product_id',
        'gross_amount', 'commission_rate', 'commission', 'net_amount', 'status', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function store()
    {
        return $this->belongsTo(Partner::class, 'store_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
