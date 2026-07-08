<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'recipient_role', 'recipient_id', 'type', 'icon', 'title', 'body', 'link', 'channels', 'read_at',
    ];

    protected $casts = [
        'channels' => 'array',
        'read_at'  => 'datetime',
    ];

    public function scopeFor($query, string $role, int $id)
    {
        return $query->where('recipient_role', $role)->where('recipient_id', $id);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
