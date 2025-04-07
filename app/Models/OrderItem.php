<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'bag_id',
        'quantity',
        'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }

    public function hasReview()
    {
        return Review::where('user_id', auth()->id())
            ->where('bag_id', $this->bag_id)
            ->where('order_id', $this->order_id)
            ->exists();
    }
}