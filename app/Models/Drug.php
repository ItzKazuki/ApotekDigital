<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'price',
        'purchase_price',
        'modal',
        'stock',
        'category_id',
        'expired_at',
        'packaging_types',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'modal' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    // buatkan saya method boot dimana barcode dibuat secara otomatis jika saat di buat memang barcode nya kosong (tidak diisi)
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($drug) {
            if (empty($drug->barcode)) {
                $drug->barcode = self::generateBarcode();
            }
        });
    }

    /**
     * Get the URL of the drug's image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : asset('images/default-drug.png');
    }

    public function getProfitAttribute()
    {
        return $this->price - $this->modal;
    }

    public function getTotalProfitAttribute()
    {
        return $this->profit * $this->stock;
    }

    public function getProfitMarginAttribute()
    {
        return $this->modal > 0 ? round(($this->profit / $this->modal) * 100, 2) : 0;
    }

    public function getPurchaseMarginAttribute()
    {
        return $this->purchase_price > 0 ? round((($this->price - $this->purchase_price) / $this->purchase_price) * 100, 2) : 0;
    }

    /**
     * Get the category that owns the drug.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    private static function generateBarcode()
    {
        do {
            $barcode = str_pad(mt_rand(0, 9999999999999), 13, '0', STR_PAD_LEFT);
        } while (Drug::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
