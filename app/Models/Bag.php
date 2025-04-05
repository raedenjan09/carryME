<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'price'];

    public function images()
    {
        return $this->hasMany(BagImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(BagImage::class)->where('is_primary', true);
    }
}
