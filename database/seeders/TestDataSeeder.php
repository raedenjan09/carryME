<?php

namespace Database\Seeders;

use App\Models\Bag;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Faker\Factory;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        // Create test bags
        for ($i = 0; $i < 10; $i++) {
            Bag::create([
                'name' => $faker->words(3, true),
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 100, 1000),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }

        // Create test users
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'user',
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);

            // Create orders for each user
            $numOrders = rand(1, 5);
            $bags = Bag::inRandomOrder()->take($numOrders)->get();
            
            foreach ($bags as $bag) {
                Order::create([
                    'user_id' => $user->id,
                    'bag_id' => $bag->id,
                    'total' => $bag->price,
                    'status' => $faker->randomElement(['pending', 'completed']),
                    'created_at' => $faker->dateTimeBetween($user->created_at, 'now'),
                    'shipping_address' => $faker->streetAddress,
                    'shipping_city' => $faker->city,
                    'shipping_country' => $faker->country,
                    'shipping_postal_code' => $faker->postcode,
                ]);
            }
        }
    }
}