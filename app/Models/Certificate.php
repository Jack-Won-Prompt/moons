<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'code', 'sell_request_id', 'brand', 'model', 'category', 'result',
        'appraiser', 'issuer', 'thumbnail', 'blockchain_hash', 'dpp', 'issued_at',
    ];

    protected $casts = [
        'dpp'       => 'array',
        'issued_at' => 'datetime',
    ];

    public function sellRequest()
    {
        return $this->belongsTo(SellRequest::class);
    }

    /** Canonical payload that the blockchain hash is computed over. */
    public function hashPayload(): string
    {
        return json_encode([
            'code'      => $this->code,
            'brand'     => $this->brand,
            'model'     => $this->model,
            'result'    => $this->result,
            'appraiser' => $this->appraiser,
            'issued_at' => optional($this->issued_at)->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function computeHash(): string
    {
        return hash('sha256', $this->hashPayload());
    }

    public function isValid(): bool
    {
        return $this->blockchain_hash === $this->computeHash();
    }

    public function getResultLabelAttribute(): string
    {
        return ['authentic' => '정품', 'fake' => '가품', 'uncertain' => '판정보류'][$this->result] ?? $this->result;
    }
}
