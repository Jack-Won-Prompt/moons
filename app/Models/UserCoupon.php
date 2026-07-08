<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $fillable = ['user_id', 'coupon_id', 'order_id', 'used_at'];

    protected $casts = ['used_at' => 'datetime'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function scopeUsable($query)
    {
        return $query->whereNull('used_at');
    }
}
