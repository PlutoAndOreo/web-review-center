<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Basic and advanced mathematics concepts',
                'is_active' => true,
            ],
            [
                'name' => 'Science',
                'code' => 'SCI',
                'description' => 'General science and scientific concepts',
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'description' => 'English language and literature',
                'is_active' => true,
            ],
            [
                'name' => 'Filipino',
                'code' => 'FIL',
                'description' => 'Filipino language and literature',
                'is_active' => true,
            ],
            [
                'name' => 'Social Studies',
                'code' => 'SOC',
                'description' => 'History, geography, and social sciences',
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}