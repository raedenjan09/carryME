<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Define the relationship with the Bag model.
     */
    public function bags()
    {
        return $this->hasMany(Bag::class);
    }
}

use App\Models\Category;

Category::firstOrCreate(
    ['slug' => 'handbags'],
    ['name' => 'Handbags', 'description' => 'Stylish handbags for every occasion.']
);

Category::firstOrCreate(
    ['slug' => 'backpacks'],
    ['name' => 'Backpacks', 'description' => 'Durable and spacious backpacks.']
);

Category::firstOrCreate(
    ['slug' => 'clutches'],
    ['name' => 'Clutches', 'description' => 'Elegant clutches for formal events.']
);
