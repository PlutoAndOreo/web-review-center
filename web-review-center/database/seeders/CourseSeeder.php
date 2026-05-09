<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Bachelor of Science in Nursing',
                'code' => 'BSN',
                'description' => 'A comprehensive program that prepares students for a career in nursing, covering fundamental nursing principles, medical-surgical care, maternal and child health, mental health nursing, and community health nursing.',
                'is_active' => true,
            ],
            [
                'title' => 'Bachelor of Science in Information Technology',
                'code' => 'BSIT',
                'description' => 'A program that focuses on the study of computer systems, software development, and information management, preparing students for careers in the IT industry.',
                'is_active' => false,
            ],
            [
                'title' => 'Bachelor of Science in Business Administration',
                'code' => 'BSBA',
                'description' => 'A program that provides students with a broad understanding of business principles, including management, marketing, finance, and entrepreneurship.',
                'is_active' => false,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
