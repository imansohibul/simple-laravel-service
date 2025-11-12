<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator
        User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin123!@#'),
            'name' => 'System Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);

        // Create Managers
        $managers = [
            ['name' => 'Alice Manager', 'email' => 'alice@example.com'],
            ['name' => 'Bob Manager', 'email' => 'bob@example.com'],
            ['name' => 'Carol Manager', 'email' => 'carol@example.com'],
        ];

        foreach ($managers as $manager) {
            User::create([
                'email' => $manager['email'],
                'password' => Hash::make('Manager123!@#'),
                'name' => $manager['name'],
                'role' => 'manager',
                'active' => true,
            ]);
        }

        // Create Regular Users (Active)
        $activeUsers = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com'],
            ['name' => 'Michael Johnson', 'email' => 'michael.j@example.com'],
            ['name' => 'Emily Davis', 'email' => 'emily.d@example.com'],
            ['name' => 'David Wilson', 'email' => 'david.w@example.com'],
            ['name' => 'Sarah Brown', 'email' => 'sarah.b@example.com'],
            ['name' => 'James Taylor', 'email' => 'james.t@example.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com'],
            ['name' => 'Robert Martinez', 'email' => 'robert.m@example.com'],
            ['name' => 'Jennifer Garcia', 'email' => 'jennifer.g@example.com'],
            ['name' => 'William Rodriguez', 'email' => 'william.r@example.com'],
            ['name' => 'Maria Lopez', 'email' => 'maria.l@example.com'],
            ['name' => 'Thomas Lee', 'email' => 'thomas.l@example.com'],
            ['name' => 'Nancy Walker', 'email' => 'nancy.w@example.com'],
            ['name' => 'Christopher Hall', 'email' => 'chris.h@example.com'],
        ];

        foreach ($activeUsers as $user) {
            User::create([
                'email' => $user['email'],
                'password' => Hash::make('User123!@#'),
                'name' => $user['name'],
                'role' => 'user',
                'active' => true,
            ]);
        }

        // Create Inactive Users (for testing filtering)
        $inactiveUsers = [
            ['name' => 'Inactive User One', 'email' => 'inactive1@example.com'],
            ['name' => 'Inactive User Two', 'email' => 'inactive2@example.com'],
            ['name' => 'Inactive User Three', 'email' => 'inactive3@example.com'],
        ];

        foreach ($inactiveUsers as $user) {
            User::create([
                'email' => $user['email'],
                'password' => Hash::make('Inactive123!@#'),
                'name' => $user['name'],
                'role' => 'user',
                'active' => false,
            ]);
        }

        $this->command->info('✓ Created 1 Administrator');
        $this->command->info('✓ Created 3 Managers');
        $this->command->info('✓ Created 15 Active Users');
        $this->command->info('✓ Created 3 Inactive Users');
        $this->command->info('Total: 22 users created successfully!');
    }
}
