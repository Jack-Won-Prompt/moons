<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'code', 'type', 'customer_id', 'store_id', 'product_id', 'sell_request_id',
        'subject', 'status', 'last_message_at', 'customer_read_at', 'staff_read_at',
    ];

    protected $casts = [
        'last_message_at'  => 'datetime',
        'customer_read_at' => 'datetime',
        'staff_read_at'    => 'datetime',
    ];

    public const TYPES = ['quote' => '견적 상담', 'product' => '상품 문의', 'support' => '고객 상담'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store()
    {
        return $this->belongsTo(Partner::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStaffLabelAttribute(): string
    {
        return $this->store?->company_name ?? 'MOONS 본사';
    }

    /** 미읽음 개수 — 상대측 메시지 기준 */
    public function unreadFor(string $side): int
    {
        $readAt = $side === 'customer' ? $this->customer_read_at : $this->staff_read_at;
        $otherRoles = $side === 'customer' ? ['store', 'admin', 'system'] : ['customer'];

        return $this->messages()
            ->whereIn('sender_role', $otherRoles)
            ->when($readAt, fn ($q) => $q->where('created_at', '>', $readAt))
            ->count();
    }
}
