<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bagsxury.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now()->subMonths(6),
            'updated_at' => now()
        ]);

        // Create 20 regular users with random creation dates
        for ($i = 1; $i <= 20; $i++) {
            $createdAt = now()->subMonths(rand(0, 5))->subDays(rand(0, 30));
            
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@bagsxury.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
        }
    }
}