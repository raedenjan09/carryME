<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        // Get some users
        $users = User::all();

        // Create sample orders
        foreach ($users as $user) {
            Order::create([
                'user_id' => $user->id,
                'total' => rand(100, 1000),
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_address' => '123 Sample St',
                'shipping_city' => 'Sample City',
                'shipping_country' => 'Sample Country',
                'shipping_postal_code' => '12345'
            ]);
        }
    }
}