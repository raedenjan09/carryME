<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmailTrait, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_picture',
        'role',
        'is_active',
    ];

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
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'user'  // Set default role
    ];

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($user) {
            if ($user->isDirty('is_active') && !$user->is_active) {
                // Force logout if user is deactivated
                if (auth()->id() === $user->id) {
                    auth()->logout();
                }
            }
        });
    }

    public function shouldVerifyEmail()
    {
        // Only verify email for non-admin users
        return !$this->isAdmin();
    }

    public function hasVerifiedEmail()
    {
        // Admins bypass email verification completely
        if ($this->role === 'admin') {
            return true;
        }
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified()
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        return parent::markEmailAsVerified();
    }

    public function sendEmailVerificationNotification()
    {
        // Don't send verification emails to admins
        if ($this->role !== 'admin') {
            parent::sendEmailVerificationNotification();
        }
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getCartItemsCountAttribute()
    {
        return $this->cart?->items->sum('quantity') ?? 0;
    }

    // Add accessor for profile picture URL
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return Storage::url($this->profile_picture);
        }
        return asset('images/default-avatar.jpg');
    }

    
}
