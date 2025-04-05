<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bag_id',
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

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }
}
