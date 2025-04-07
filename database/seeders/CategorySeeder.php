<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Shoulder Bags',
            'Totes',
            'Clutches',
            'Backpacks',
            'Cross-body Bags'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => "Collection of $categoryName"
            ]);
        }
    }
}