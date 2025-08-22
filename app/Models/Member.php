<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'expires_at',
        'point',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'point' => 'decimal:2',
        ];
    }

    /**
     * Get the transactions for the member.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getStatusAttribute()
    {
        return $this->expires_at && $this->expires_at->isFuture() ? 'active' : 'inactive';
    }

    public function calculateMemberRewards($transactionAmount)
    {
        $baseThreshold = 20000;
        $pointStep = 25000;

        $addDays = 0;
        $totalPoints = 0;

        if ($transactionAmount > $baseThreshold) {
            // Tambah expired 15 hari
            $addDays = 15;

            // Hitung jumlah kelipatan
            $multiples = floor($transactionAmount / $pointStep);

            // Hitung total poin berdasarkan rumus: 10 + 12 + 14 + ... (2n + 8)
            for ($i = 1; $i <= $multiples; $i++) {
                $totalPoints += (2 * $i) + 8;
            }
        }

        return [
            'add_days' => $addDays,
            'point' => $totalPoints,
        ];
    }

    public function checkPromoMember(): array | null
    {
        $promoList = config('promo.list');
        $bestPromo = null;

        foreach ($promoList as $promoItem) {
            if ($this->point >= $promoItem['min_point'] && $this->point >= $promoItem['use_point']) {
                // Selalu pilih promo dengan min_point terbesar yang masih <= poin member
                $bestPromo = [
                    'discount' => $promoItem['discount'],
                    'description' => $promoItem['description'],
                    'used_point' => $promoItem['use_point']
                ];
            }
        }

        return $bestPromo;
    }
}
