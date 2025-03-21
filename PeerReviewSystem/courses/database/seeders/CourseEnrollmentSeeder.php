<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseEnrollmentSeeder extends Seeder
{
    public function run()
    {
        // Fetch all student IDs
        $students = DB::table('users')->where('role', 'student')->pluck('id')->toArray();
        
        // Define the possible course IDs (from 1 to 5)
        $courseIds = [1, 2, 3, 4, 5];
        
        $enrollments = [];
        
        // Randomly assign students to courses
        foreach ($students as $student_id) {
            // Random number of courses each student should be enrolled in (between 1 and 3 courses)
            $randomCourses = array_rand($courseIds, rand(1, 3));

            // If only one course is selected, array_rand returns a single value, so wrap it in an array
            if (!is_array($randomCourses)) {
                $randomCourses = [$randomCourses];
            }

            // Create an enrollment entry for each selected course
            foreach ($randomCourses as $courseIndex) {
                $enrollments[] = [
                    'user_id' => $student_id,
                    'course_id' => $courseIds[$courseIndex],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert enrollments into the enrollments table
        DB::table('enrollments')->insert($enrollments);
        
        $this->command->info('Random student enrollments have been seeded successfully.');
    }
}
