<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'partner_id',
        'brand',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'image',
        'gallery',
        'source_no',
        'color',
        'stock',
        'is_new',
        'is_best',
        'is_active',
        'view_count',
    ];

    protected $casts = [
        'is_new'    => 'boolean',
        'is_best'   => 'boolean',
        'is_active' => 'boolean',
        'gallery'   => 'array',
    ];

    /** 판매자 라벨: 본사(partner 없음) 또는 지점명 */
    public function getSellerLabelAttribute(): string
    {
        return $this->partner?->company_name ?? 'MOONS 본사';
    }

    /** 판매자 유형: head_office / store */
    public function getSellerTypeAttribute(): string
    {
        return $this->partner_id ? 'store' : 'head_office';
    }

    /** Main image URL: full remote URL as-is, local relative path via asset(), else null. */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Effective selling price. */
    public function getFinalPriceAttribute(): int
    {
        return $this->sale_price ?: $this->price;
    }

    /** Discount rate as integer percent, or 0 when not discounted. */
    public function getDiscountRateAttribute(): int
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }

        return (int) round(($this->price - $this->sale_price) / $this->price * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
