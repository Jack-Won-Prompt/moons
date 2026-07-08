<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['code', 'title', 'subtitle', 'description', 'thumbnail', 'gradient', 'filters', 'is_active'];

    protected $casts = ['filters' => 'array', 'is_active' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** 기획전 조건에 맞는 상품 쿼리 */
    public function products()
    {
        $q = Product::active()->whereNotNull('image');
        $f = $this->filters ?? [];

        if (! empty($f['brand'])) {
            $q->where('brand', $f['brand']);
        }
        if (! empty($f['category_id'])) {
            $q->where('category_id', $f['category_id']);
        }
        if (! empty($f['min_discount'])) {
            $q->whereNotNull('sale_price')
                ->whereRaw('(price - sale_price) / price * 100 >= ?', [$f['min_discount']]);
        }

        return $q;
    }
}
