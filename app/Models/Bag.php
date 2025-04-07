<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock',
        'category_id'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($bag) {
            if (!$bag->slug) {
                $bag->slug = Str::slug($bag->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(BagImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(BagImage::class)->where('is_primary', true);
    }

    public function getPrimaryImagePathAttribute()
    {
        return $this->primaryImage->image_path ?? 'placeholder.jpg';
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class)->whereHas('order', function($query) {
            $query->where('status', 'delivered');
        });
    }

    public function uniqueBuyers()
    {
        return $this->orders()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('orders.user_id')
            ->distinct()
            ->count();
    }
}


