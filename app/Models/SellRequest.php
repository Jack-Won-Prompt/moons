<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellRequest extends Model
{
    protected $fillable = [
        'code', 'customer_id', 'target_type', 'target_store_id', 'category_id',
        'brand', 'title', 'description', 'photos', 'method', 'delivery_method',
        'visit_at', 'desired_price', 'status', 'appraisal', 'appraisal_result',
        'appraiser', 'quote_price', 'winning_store_id', 'memo',
    ];

    protected $casts = [
        'photos'    => 'array',
        'appraisal' => 'array',
        'visit_at'  => 'datetime',
    ];

    /** 상태 라벨 + 색상(pill 클래스) */
    public const STATUSES = [
        'received'          => ['접수', 'gray'],
        'appraising'        => ['감정중', 'amber'],
        'photo_requested'   => ['사진 보완요청', 'amber'],
        'quoting'           => ['견적 진행', 'amber'],
        'auctioning'        => ['경매 진행', 'amber'],
        'quoted'            => ['견적 완료', 'green'],
        'customer_approved' => ['고객 승인', 'green'],
        'inbound'           => ['입고', 'green'],
        'settled'           => ['정산 완료', 'green'],
        'rejected'          => ['반려', 'red'],
    ];

    public const CHECKLIST = [
        'brand'      => '브랜드 확인',
        'model'      => '모델 확인',
        'serial'     => '시리얼 번호 확인',
        'logo'       => '로고 확인',
        'stitching'  => '박음질 확인',
        'leather'    => '가죽 확인',
        'metal'      => '금속 부품 확인',
        'components' => '구성품 확인',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status][0] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status][1] ?? 'gray';
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store()
    {
        return $this->belongsTo(Partner::class, 'target_store_id');
    }

    public function winningStore()
    {
        return $this->belongsTo(Partner::class, 'winning_store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class)->orderByDesc('bid_price');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function getTargetLabelAttribute(): string
    {
        return $this->target_type === 'store'
            ? ($this->store?->company_name ?? '지점')
            : 'MOONS 본사';
    }
}
