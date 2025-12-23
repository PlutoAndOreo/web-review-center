<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rc_students')->insert([
            [
                'first_name' => 'student',
                'last_name' => 'test',
                'email' => 'student@test.com',
                'phone' => '09170000001',
                'birthdate' => '2000-05-15',
                'gender' => 'Male',
                'student_number' => 'STU-0001',
                'course' => 'Computer Science',
                'created_at' => now(),
                'updated_at' => now(),
                'password' => Hash::make('test123'),
            ],
        ]);
    }
}
