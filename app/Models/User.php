<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'provider', 'provider_id', 'avatar',
        'grade', 'points', 'total_spent', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** 등급 => [라벨, 최소누적금액, 적립률, 색상] */
    public const GRADES = [
        'bronze' => ['브론즈', 0,        0.01, '#cd7f32'],
        'silver' => ['실버',   1000000,  0.02, '#9ca3af'],
        'gold'   => ['골드',   5000000,  0.03, '#d4af37'],
        'vip'    => ['VIP',    20000000, 0.05, '#7c3aed'],
    ];

    public function gradeInfo(): array
    {
        return self::GRADES[$this->grade] ?? self::GRADES['bronze'];
    }

    public function getGradeLabelAttribute(): string
    {
        return $this->gradeInfo()[0];
    }

    public function pointRate(): float
    {
        return $this->gradeInfo()[2];
    }

    /** 누적 구매액 기준 등급 재계산 */
    public function recalcGrade(): void
    {
        $grade = 'bronze';
        foreach (self::GRADES as $key => [$label, $min]) {
            if ($this->total_spent >= $min) {
                $grade = $key;
            }
        }
        if ($this->grade !== $grade) {
            $this->grade = $grade;
            $this->save();
        }
    }

    public function coupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->latest();
    }
}
