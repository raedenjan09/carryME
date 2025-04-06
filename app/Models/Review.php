<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['bag_id', 'user_id', 'rating', 'comment'];

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}