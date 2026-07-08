<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    protected $fillable = [
        'sell_request_id', 'store_id', 'bid_price', 'message', 'status',
    ];

    public function sellRequest()
    {
        return $this->belongsTo(SellRequest::class);
    }

    public function store()
    {
        return $this->belongsTo(Partner::class, 'store_id');
    }
}
