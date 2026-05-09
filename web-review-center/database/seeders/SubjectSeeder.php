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
                'course_id' => 1,
                'description' => 'Basic and advanced mathematics concepts',
                'is_active' => true,
            ],
            [
                'name' => 'Science',
                'course_id' => 1,
                'description' => 'General science and scientific concepts',
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'course_id' => 1,
                'description' => 'English language and literature',
                'is_active' => true,
            ],
            [
                'name' => 'Filipino',
                'course_id' => 1,
                'description' => 'Filipino language and literature',
                'is_active' => true,
            ],
            [
                'name' => 'Social Studies',
                'course_id' => 1,
                'description' => 'History, geography, and social sciences',
                'is_active' => true,
            ],
            // Core Nursing Subjects
            [
                'name' => 'Fundamentals of Nursing',
                'course_id' => 1,
                'description' => 'Basic principles, concepts, and foundations of nursing practice.',
                'is_active' => true,
            ],
            [
                'name' => 'Medical Surgical Nursing',
                'course_id' => 1,
                'description' => 'Care of adult patients with medical and surgical conditions.',
                'is_active' => true,
            ],
            [
                'name' => 'Mother and Child Nursing',
                'course_id' => 1,
                'description' => 'Comprehensive nursing care for mothers, infants, and children.',
                'is_active' => true,
            ],
            [
                'name' => 'Obstetric Nursing',
                'course_id' => 1,
                'description' => 'Nursing care during pregnancy, labor, delivery, and postpartum.',
                'is_active' => true,
            ],
            [
                'name' => 'Pediatric Nursing',
                'course_id' => 1,
                'description' => 'Nursing care of infants, children, and adolescents.',
                'is_active' => true,
            ],
            [
                'name' => 'Community Health Nursing',
                'course_id' => 1,
                'description' => 'Health promotion and disease prevention in community settings.',
                'is_active' => true,
            ],
            [
                'name' => 'Communicable Disease Nursing',
                'course_id' => 1,
                'description' => 'Prevention, control, and management of communicable diseases.',
                'is_active' => true,
            ],
            [
                'name' => 'Psychiatric Nursing',
                'course_id' => 1,
                'description' => 'Mental health and psychiatric nursing care.',
                'is_active' => true,
            ],
            [
                'name' => 'Professional Adjustment Nursing',
                'course_id' => 1,
                'description' => 'Ethics, legal issues, professional roles, and career preparedness.',
                'is_active' => true,
            ],
            [
                'name' => 'Research Nursing',
                'course_id' => 1,
                'description' => 'Nursing research principles and evidence-based practice.',
                'is_active' => true,
            ],
            [
                'name' => 'Leadership and Management Nursing',
                'course_id' => 1,
                'description' => 'Leadership, management, and supervisory roles in nursing.',
                'is_active' => true,
            ],

            // Enrichment Topics
            [
                'name' => 'Perioperative Nursing',
                'course_id' => 1,
                'description' => 'Preoperative, intraoperative, and postoperative nursing care.',
                'is_active' => true,
            ],
            [
                'name' => 'Disaster and Emergency Nursing',
                'course_id' => 1,
                'description' => 'Emergency response and disaster preparedness nursing.',
                'is_active' => true,
            ],
            [
                'name' => 'Nursing Informatics',
                'course_id' => 1,
                'description' => 'Use of information technology in nursing practice.',
                'is_active' => true,
            ],
            [
                'name' => 'Basic and Advanced Life Support',
                'course_id' => 1,
                'description' => 'Life-saving procedures including BLS and ALS protocols.',
                'is_active' => true,
            ],
            [
                'name' => 'ECG Interpretation',
                'course_id' => 1,
                'description' => 'Understanding and interpretation of electrocardiograms.',
                'is_active' => true,
            ],
            [
                'name' => 'Pharmacology Nursing',
                'course_id' => 1,
                'description' => 'Drug classifications, actions, and nursing responsibilities.',
                'is_active' => true,
            ],

            // Final Coaching
            [
                'name' => 'Ultimate Final Coaching',
                'course_id' => 1,
                'description' => 'Comprehensive final review and board exam preparation.',
                'is_active' => true,
            ],
            [
                'name' => 'Pre-Board Rationalizations',
                'course_id' => 1,
                'description' => 'Pre-board exam rationales and intensive review discussions.',
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}