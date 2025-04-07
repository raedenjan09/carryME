<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        \App\Models\Category::truncate();
        \App\Models\Bag::truncate();
        \App\Models\Order::truncate();
        \App\Models\OrderItem::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Run seeders in order
        $this->call([
            CategorySeeder::class,
            UsersTableSeeder::class,
            BagSeeder::class
        ]);
    }
}
