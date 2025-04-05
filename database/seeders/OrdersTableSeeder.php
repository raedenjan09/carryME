<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Bag;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $bags = Bag::all();

        if ($bags->isEmpty()) {
            // Create some bags if none exist
            for ($i = 0; $i < 5; $i++) {
                Bag::create([
                    'name' => "Sample Bag $i",
                    'description' => "Description for bag $i",
                    'price' => rand(100, 1000)
                ]);
            }
            $bags = Bag::all();
        }

        foreach ($users as $user) {
            // Create 1-5 orders per user with random dates
            $orderCount = rand(1, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $bag = $bags->random();
                $createdAt = now()
                    ->subMonths(rand(0, 5))
                    ->subDays(rand(0, 30))
                    ->subHours(rand(0, 23));
                
                Order::create([
                    'user_id' => $user->id,
                    'bag_id' => $bag->id,
                    'total' => $bag->price,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'shipping_address' => '123 Sample St',
                    'shipping_city' => 'Sample City',
                    'shipping_country' => 'Sample Country',
                    'shipping_postal_code' => '12345',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt
                ]);
            }
        }
    }
}