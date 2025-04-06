<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagImage extends Model
{
    use HasFactory;

    protected $fillable = ['bag_id', 'image_path', 'is_primary'];

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }

    public function getImagePathAttribute($value)
    {
        return basename($value); // This will return just the filename
    }
}