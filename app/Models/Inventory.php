<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = ['store_id', 'product_id', 'quantity', 'location'];

    public function store()
    {
        return $this->belongsTo(Partner::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
