<?php

namespace Database\Seeders;

use App\Models\Bag;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BagSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            throw new \Exception('Categories must exist before seeding bags.');
        }

        $bags = [
            [
                'name' => 'Classic Leather Tote',
                'description' => 'A timeless leather tote bag perfect for everyday use',
                'price' => 299.99,
                'image' => 'bags/tote-1.jpg',
                'category' => 'Totes'
            ],
            [
                'name' => 'Evening Clutch',
                'description' => 'Elegant evening clutch with gold hardware',
                'price' => 149.99,
                'image' => 'bags/clutch-1.jpg',
                'category' => 'Clutches'
            ],
            [
                'name' => 'Urban Backpack',
                'description' => 'Modern backpack with laptop compartment',
                'price' => 199.99,
                'image' => 'bags/backpack-1.jpg',
                'category' => 'Backpacks'
            ],
            // Add more bags as needed
        ];

        foreach ($bags as $bagData) {
            $category = $categories->where('name', $bagData['category'])->first() 
                       ?? $categories->first();

            Bag::create([
                'name' => $bagData['name'],
                'slug' => Str::slug($bagData['name']),
                'description' => $bagData['description'],
                'price' => $bagData['price'],
                'image' => $bagData['image'],
                'category_id' => $category->id,
                'stock' => rand(5, 50)
            ]);
        }
    }
}