<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'order_id', 'rating', 'body', 'photos'];

    protected $casts = ['photos' => 'array'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithPhotos($query)
    {
        return $query->whereNotNull('photos')->where('photos', '!=', '[]');
    }
}
