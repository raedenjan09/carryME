<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Bag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();
            
            $users = User::all();
            $bags = Bag::all();

            if ($bags->isEmpty()) {
                // Create sample bags
                $sampleBags = [
                    [
                        'name' => 'Luxury Tote Bag',
                        'description' => 'Premium leather tote bag',
                        'price' => 299.99,
                        'image' => 'bags/tote.jpg'
                    ],
                    [
                        'name' => 'Designer Clutch',
                        'description' => 'Elegant evening clutch',
                        'price' => 149.99,
                        'image' => 'bags/clutch.jpg'
                    ],
                    // Add more sample bags as needed
                ];

                foreach ($sampleBags as $bagData) {
                    Bag::create([
                        'name' => $bagData['name'],
                        'slug' => Str::slug($bagData['name']),
                        'description' => $bagData['description'],
                        'price' => $bagData['price'],
                        'image' => $bagData['image'],
                        'stock' => rand(5, 20),
                        'category_id' => 1
                    ]);
                }

                $bags = Bag::all();
            }

            foreach ($users as $user) {
                $orderCount = rand(1, 5);
                
                for ($i = 0; $i < $orderCount; $i++) {
                    $bag = $bags->random();
                    $quantity = rand(1, 3);
                    $createdAt = now()
                        ->subMonths(rand(0, 5))
                        ->subDays(rand(0, 30))
                        ->subHours(rand(0, 23));
                    
                    $order = Order::create([
                        'user_id' => $user->id,
                        'total' => $bag->price * $quantity,
                        'status' => 'completed',
                        'shipping_address' => '123 Sample St',
                        'shipping_city' => 'Sample City',
                        'shipping_country' => 'Philippines',
                        'shipping_postal_code' => '12345',
                        'payment_method' => 'credit_card',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'bag_id' => $bag->id,
                        'quantity' => $quantity,
                        'price' => $bag->price
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}