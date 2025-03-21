<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseTeacherTableSeeder extends Seeder
{
    public function run()
    {
        // Get all teacher IDs
        $teachers = DB::table('users')->where('role', 'Lecturer')->pluck('id')->toArray();
        
        // Get all course IDs
        $courses = DB::table('courses')->pluck('id')->toArray();

        // Shuffle teachers for random assignment
        shuffle($teachers);

        // Assign each course one main lecturer and 1 assistant lecturer
        foreach ($courses as $course_id) {
            // Ensure there are enough teachers
            if (count($teachers) < 2) { // 1 main and at least 1 assistant
                // Reload teachers and shuffle again if needed
                $teachers = DB::table('users')->where('role', 'Lecturer')->pluck('id')->toArray();
                shuffle($teachers);
            }

            // Pick one main lecturer (ensure uniqueness per course)
            $mainLecturer = array_shift($teachers);

            // Check if a main lecturer is already assigned for the course
            $mainExists = DB::table('course_teacher')
                ->where('course_id', $course_id)
                ->where('role', 'main')
                ->exists();

            if (!$mainExists) {
                DB::table('course_teacher')->insert([
                    'user_id' => $mainLecturer,
                    'course_id' => $course_id,
                    'role' => 'main', // Assign as main lecturer
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Now exclude the main lecturer and assign 1 assistant
            $availableTeachers = array_diff($teachers, [$mainLecturer]); // Remove main from available teachers
            
            // Pick one assistant lecturer
            $assistantLecturer = array_shift($availableTeachers);

            // Check if this specific assistant is already assigned for this course
            $assistantExists = DB::table('course_teacher')
                ->where('course_id', $course_id)
                ->where('user_id', $assistantLecturer)
                ->where('role', 'assistant')
                ->exists();

            if (!$assistantExists) {
                DB::table('course_teacher')->insert([
                    'user_id' => $assistantLecturer,
                    'course_id' => $course_id,
                    'role' => 'assistant', // Assign as assistant lecturer
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
