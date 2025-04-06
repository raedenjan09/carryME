<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Handbags', 'slug' => 'handbags', 'description' => 'Stylish handbags for every occasion.'],
            ['name' => 'Backpacks', 'slug' => 'backpacks', 'description' => 'Durable and spacious backpacks.'],
            ['name' => 'Clutches', 'slug' => 'clutches', 'description' => 'Elegant clutches for formal events.'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']], // Check for an existing slug
                $categoryData // Insert if it doesn't exist
            );
        }
    }
}