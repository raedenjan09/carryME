<?php

namespace Database\Seeders;

use App\Models\Bag;
use Illuminate\Database\Seeder;

class BagSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            Bag::create([
                'name' => "Product $i",
                'description' => "This is the description for Product $i.",
                'price' => rand(100, 1000),
                'image' => 'bags/product' . $i . '.jpg',
            ]);
        }

        Bag::create([
            'name' => 'Leather Bag',
            'description' => 'A premium quality leather bag.',
            'price' => 150.00,
            'image' => 'bags/leather.jpg',
        ]);
    }
}