<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\StoreBase64Image;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, StoreBase64Image;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_logged_in',
        'profile_image',
    ];

    const PROFILE_IMAGE_PATH = 'images/users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // prevent avatar called many time.
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->profile_image)) {
                $user->profile_image = $user->storeBase64Image(self::PROFILE_IMAGE_PATH,  Avatar::create($user->name)->toBase64());
            }
        });

        static::updating(function ($user) {
            // cek apakah kolom profile_image berubah
            if ($user->isDirty('profile_image')) {
                $oldImage = $user->getOriginal('profile_image');
                if ($oldImage && Storage::exists($oldImage)) {
                    Storage::delete($oldImage);
                }
            }
        });

        static::deleting(function ($user) {
            if (Storage::exists($user->profile_image)) {
                Storage::delete($user->profile_image);
            }
        });
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }

    /**
     * Get url for the user's profile image.
     */
    public function getProfileImageUrlAttribute(): string | null
    {
        return $this->profile_image && Storage::exists($this->profile_image) ? asset('storage/' . $this->profile_image) : Avatar::create($this->name)->toBase64();
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'kasir_id');
    }
}
