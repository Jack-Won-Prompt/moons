<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Product count including all descendant categories. */
    public function allProducts()
    {
        $ids = $this->children()->pluck('id')->push($this->id);

        return Product::whereIn('category_id', $ids);
    }
}
