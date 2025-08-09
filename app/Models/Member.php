<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
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
}
