<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : asset('images/default-category.png');
    }

    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }
}
