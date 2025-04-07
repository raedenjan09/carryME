<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'bag_id',
        'order_id',
        'rating',
        'comment',
        'is_anonymous'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getReviewerNameAttribute()
    {
        return $this->is_anonymous ? 'Anonymous User' : $this->user->name;
    }
}