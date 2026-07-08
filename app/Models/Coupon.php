<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'name', 'type', 'value', 'min_order', 'max_discount', 'expires_at', 'is_active'];

    protected $casts = ['expires_at' => 'date', 'is_active' => 'boolean'];

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    /** 주문금액에 대한 할인액 계산 */
    public function discountFor(int $amount): int
    {
        if ($amount < $this->min_order) {
            return 0;
        }
        $d = $this->type === 'percent' ? (int) floor($amount * $this->value / 100) : (int) $this->value;
        if ($this->max_discount) {
            $d = min($d, $this->max_discount);
        }

        return min($d, $amount);
    }

    public function getLabelAttribute(): string
    {
        return $this->type === 'percent' ? "{$this->value}% 할인" : number_format($this->value) . '원 할인';
    }

    public function isValid(): bool
    {
        return $this->is_active && (! $this->expires_at || $this->expires_at->isFuture() || $this->expires_at->isToday());
    }
}
