<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rc_admins')->insert([
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin123'),
                'phone' => '09171234567',
                'role' => 'super_admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Regular',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'phone' => '09179876543',
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
