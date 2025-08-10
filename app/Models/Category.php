<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path'
    ];

    const CATEGORY_IMAGE_PATH = 'images/categories';

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($category) {
            // cek apakah kolom profile_image berubah
            if ($category->isDirty('image_path')) {
                $oldImage = $category->getOriginal('image_path');
                if ($oldImage && Storage::exists($oldImage)) {
                    Storage::delete($oldImage);
                }
            }
        });

        static::deleting(function ($category) {
            if (Storage::exists($category->image_path)) {
                Storage::delete($category->image_path);
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path && Storage::exists($this->image_path) ? asset('storage/' . $this->image_path) : asset('images/default-category.png');
    }

    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }
}
