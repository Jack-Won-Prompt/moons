<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['category', 'title', 'body', 'is_pinned'];

    protected $casts = ['is_pinned' => 'boolean'];
}
