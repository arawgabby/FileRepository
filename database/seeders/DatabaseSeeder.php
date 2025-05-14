<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insert roles and get their IDs
        $roles = [
            'admin' => DB::table('roles')->insertGetId(['name' => 'admin']),
            'staff' => DB::table('roles')->insertGetId(['name' => 'staff']),
            'faculty' => DB::table('roles')->insertGetId(['name' => 'faculty']),
        ];

        // Insert users with role_id
        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'),
                'role_id' => $roles['admin'],
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
                'password' => bcrypt('staff123'),
                'role_id' => $roles['staff'],
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
                'password' => bcrypt('user123'),
                'role_id' => $roles['faculty'],
                'contact_number' => '09170001111',
                'remember_token' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
