<?php

namespace Database\Seeders;

use App\Models\Bag;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        try {
            DB::beginTransaction();

            // Get existing categories
            $categories = Category::all();

            if ($categories->isEmpty()) {
                throw new \Exception('No categories found. Please run CategorySeeder first.');
            }

            // Create test bags
            $bags = collect();
            for ($i = 0; $i < 10; $i++) {
                $name = $faker->words(3, true);
                $bags->push(Bag::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $faker->paragraph,
                    'price' => $faker->randomFloat(2, 100, 1000),
                    'stock' => $faker->numberBetween(5, 50),
                    'category_id' => $categories->random()->id,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]));
            }

            // Create test users
            for ($i = 0; $i < 5; $i++) {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'role' => 'user',
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);

                // Create 1-3 orders for each user
                $numOrders = rand(1, 3);
                
                for ($j = 0; $j < $numOrders; $j++) {
                    $orderItems = [];
                    $total = 0;
                    
                    // Prepare order items first
                    $numItems = rand(1, 3);
                    $selectedBags = $bags->random($numItems);
                    
                    foreach ($selectedBags as $bag) {
                        $quantity = rand(1, 3);
                        $price = $bag->price;
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                        
                        $orderItems[] = [
                            'bag_id' => $bag->id,
                            'quantity' => $quantity,
                            'price' => $price
                        ];
                    }

                    // Create order with calculated total
                    $order = Order::create([
                        'user_id' => $user->id,
                        'total' => $total,
                        'status' => $faker->randomElement(['pending', 'processing', 'completed']),
                        'created_at' => $faker->dateTimeBetween($user->created_at, 'now'),
                        'shipping_address' => $faker->streetAddress,
                        'shipping_city' => $faker->city,
                        'shipping_state' => $faker->state,
                        'shipping_zipcode' => $faker->postcode,
                        'payment_method' => $faker->randomElement(['credit_card', 'paypal'])
                    ]);

                    // Create order items
                    foreach ($orderItems as $item) {
                        $order->items()->create($item);
                    }
                }
            }

            // Create admin user
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}