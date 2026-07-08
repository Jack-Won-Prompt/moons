<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = ['user_id', 'amount', 'reason', 'order_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
