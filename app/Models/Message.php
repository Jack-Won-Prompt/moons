<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id', 'sender_role', 'sender_id', 'sender_name',
        'body', 'attachment', 'attachment_type',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
