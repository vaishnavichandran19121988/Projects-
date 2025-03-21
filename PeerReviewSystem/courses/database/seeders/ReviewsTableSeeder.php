<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        // Get assessment data to match number_of_reviews and type
        $assessments = DB::table('assessments')->get();
        
        // Get enrolled students from enrollments
        $enrollments = DB::table('enrollments')->get();
        
        // Group students by course for student-selected reviews
        $course_enrollments = $enrollments->groupBy('course_id');

        foreach ($assessments as $assessment) {
            $course_id = $assessment->course_id;
            $number_of_reviews = $assessment->number_of_reviews;
            $assessment_type = $assessment->type;
            $students_in_course = $course_enrollments[$course_id];

            if ($assessment_type == 'student-select') {
                // Handle student-select type assessments
                foreach ($students_in_course as $student) {
                    $reviewer_id = $student->user_id;

                    // Randomly pick students to review, excluding the reviewer themselves
                    $reviewees = $students_in_course->where('user_id', '!=', $reviewer_id)->random($number_of_reviews);

                    foreach ($reviewees as $reviewee) {
                        $this->createReview($reviewer_id, $reviewee->user_id, $assessment->id);
                    }
                }
            } elseif ($assessment_type == 'teacher-assign') {
                // Handle teacher-assign type assessments
                // Grouping for pair review (round-robin or specific pairings)
                $this->assignTeacherGroupReviews($students_in_course, $assessment);
            }
        }
    }

    /**
     * Create a review entry
     */
    private function createReview($reviewer_id, $reviewee_id, $assessment_id)
    {
        DB::table('reviews')->insert([
            'submission_id' => DB::table('submissions')
                ->where('user_id', $reviewee_id)
                ->where('assessment_id', $assessment_id)
                ->first()->id, // Use the submission ID of the reviewee
            'reviewer_id' => $reviewer_id,
            'reviewee_id' => $reviewee_id,
            'assessment_id' => $assessment_id,
            'comments' => $this->generateReviewComments($reviewee_id),
            'score' => rand(70, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Generate review comments for a reviewee
     */
    private function generateReviewComments($reviewee_id)
    {
        $reviewee_name = DB::table('users')->where('id', $reviewee_id)->first()->name;

        return "1. Exercise Completion: Yes, the exercise has been attempted and demonstrated.\n
                2. Attempt Assessment: The code works and fulfills the requirements of the exercise. \n
                3. Quality of Submission: The code functions well but could use more readability and structure.\n
                4. Quality of Demonstration: $reviewee_name has a good understanding of the code.";
    }

    /**
     * Assign reviews in a round-robin or pairwise manner for teacher-assign assessments
     */
    private function assignTeacherGroupReviews($students, $assessment)
    {
        $student_ids = $students->pluck('user_id')->toArray();
        $num_students = count($student_ids);

        for ($i = 0; $i < $num_students; $i++) {
            $reviewer_id = $student_ids[$i];
            $reviewee_id = $student_ids[($i + 1) % $num_students]; // Round-robin: next student reviews previous student

            // Avoid self-review (just a safeguard)
            if ($reviewer_id !== $reviewee_id) {
                $this->createReview($reviewer_id, $reviewee_id, $assessment->id);
            }
        }
    }
}
