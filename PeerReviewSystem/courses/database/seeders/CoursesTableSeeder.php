<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            ['course_code' => '7611ICT', 'course_name' => 'Computer Systems and Cyber Security'],
            ['course_code' => '7001ICT', 'course_name' => 'Programming Principles'],
            ['course_code' => '7003ICT', 'course_name' => 'Database Design'],
            ['course_code' => '7002ICT', 'course_name' => 'Systems Development'],
            ['course_code' => '7610ICT', 'course_name' => 'Application Systems'],
            ['course_code' => '7612ICT', 'course_name' => 'Data Structures and Algorithms'],
            ['course_code' => '7004ICT', 'course_name' => 'Software Engineering Fundamentals'],
            ['course_code' => '7005ICT', 'course_name' => 'Web Development and Design'],
            ['course_code' => '7006ICT', 'course_name' => 'Human-Computer Interaction'],
            ['course_code' => '7007ICT', 'course_name' => 'Artificial Intelligence'],
            ['course_code' => '7613ICT', 'course_name' => 'Machine Learning and Data Mining'],
            ['course_code' => '7614ICT', 'course_name' => 'Mobile Application Development'],
            ['course_code' => '7615ICT', 'course_name' => 'Cloud Computing'],
            ['course_code' => '7616ICT', 'course_name' => 'Cyber Law and Ethics'],
            ['course_code' => '7617ICT', 'course_name' => 'Advanced Database Systems']
        ]);
    }
}

