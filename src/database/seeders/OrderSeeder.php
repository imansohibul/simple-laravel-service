<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active users
        $activeUsers = User::where('active', true)->get();

        $totalOrders = 0;

        foreach ($activeUsers as $user) {
            // Different order counts based on role for realistic data
            $orderCount = match($user->role) {
                'administrator' => rand(20, 30),  // Admin has more orders
                'manager' => rand(10, 20),        // Manager has medium orders
                'user' => rand(3, 15),            // Regular user varies
                default => rand(1, 5),
            };

            // Create orders with various timestamps
            for ($i = 0; $i < $orderCount; $i++) {
                Order::create([
                    'user_id' => $user->id,
                    'created_at' => now()->subDays(rand(1, 365)),
                ]);
                $totalOrders++;
            }
        }

        $this->command->info("✓ Created {$totalOrders} orders for " . $activeUsers->count() . " active users");
        $this->command->info('Orders seeded successfully!');
    }
}
