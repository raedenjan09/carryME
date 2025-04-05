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
        \App\Models\Bag::truncate();
        \App\Models\Order::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UsersTableSeeder::class,
            TestDataSeeder::class,
            OrdersTableSeeder::class,
        ]);
    }
}
