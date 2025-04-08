<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_postal_code'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

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

    // Add accessor to always return valid total
    public function getTotalAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
}
