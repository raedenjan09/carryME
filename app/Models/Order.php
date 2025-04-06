<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_status',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_postal_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function bags()
    {
        return $this->belongsToMany(Bag::class, 'order_items')
                    ->withPivot('quantity', 'price');
    }
}
