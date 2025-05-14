<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'), // Change as needed
                'role' => 'admin',
                'contact_number' => '09171234567',
                'remember_token' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('staff123'), // Change as needed
                'role' => 'staff',
                'contact_number' => '09179876543',
                'remember_token' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('user123'), // Change as needed
                'role' => 'user',
                'contact_number' => '09170001111',
                'remember_token' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
