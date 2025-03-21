<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import for date handling

class AssessmentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('assessments')->insert([
            [
                'title' => 'Midterm Peer Review',
                'instruction' => 'Please review your peers based on their presentation performance.',
                'number_of_reviews' => 3,
                'max_score' => 100,
                'due_date' => Carbon::create(2024, 11, 1, 17, 00, 00),
                'type' => 'student-select',
                'course_id' => 1,
                'file_path' => 'files/midterm_peer_review.pdf', // Example file
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Final Peer Review',
                'instruction' => 'Assess the final project of your peers based on the grading rubric.',
                'number_of_reviews' => 2,
                'max_score' => 100,
                'due_date' => Carbon::create(2024, 12, 1, 23, 59, 59),
                'type' => 'teacher-assign',
                'course_id' => 1,
                'file_path' => null, // No file for this assessment
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Project Peer Review',
                'instruction' => 'Review the group project work and give your feedback.',
                'number_of_reviews' => 3,
                'max_score' => 100,
                'due_date' => Carbon::create(2024, 11, 15, 16, 00, 00),
                'type' => 'student-select',
                'course_id' => 2,
                'file_path' => 'files/project_peer_review.pdf', // Example file
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Lab Work Peer Review',
                'instruction' => 'Evaluate your peer’s lab reports based on accuracy and quality.',
                'number_of_reviews' => 2,
                'max_score' => 50,
                'due_date' => Carbon::create(2024, 11, 25, 15, 00, 00),
                'type' => 'teacher-assign',
                'course_id' => 3,
                'file_path' => null, // No file for this assessment
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Final Project Peer Review',
                'instruction' => 'Evaluate your peer’s final project report.',
                'number_of_reviews' => 3,
                'max_score' => 100,
                'due_date' => Carbon::create(2024, 12, 10, 23, 59, 59),
                'type' => 'student-select',
                'course_id' => 4,
                'file_path' => 'files/final_project_peer_review.docx', // Example file
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Research Paper Peer Review',
                'instruction' => 'Review your peer’s research paper and provide constructive feedback.',
                'number_of_reviews' => 4,
                'max_score' => 100,
                'due_date' => Carbon::create(2024, 11, 20, 18, 00, 00),
                'type' => 'student-select',
                'course_id' => 5,
                'file_path' => 'files/research_paper_peer_review.pdf', // Example file
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Weekly Assignment Peer Review',
                'instruction' => 'Provide feedback on your peer’s weekly assignment.',
                'number_of_reviews' => 2,
                'max_score' => 50,
                'due_date' => Carbon::create(2024, 11, 5, 12, 00, 00),
                'type' => 'teacher-assign',
                'course_id' => 5,
                'file_path' => null, // No file for this assessment
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Code Review Peer Assessment',
                'instruction' => 'Review the code submitted by your peers and provide feedback.',
                'number_of_reviews' => 2,
                'max_score' => 75,
                'due_date' => Carbon::create(2024, 11, 30, 14, 00, 00),
                'type' => 'teacher-assign',
                'course_id' => 6,
                'file_path' => 'files/code_review_peer_assessment.zip', // Example file
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
