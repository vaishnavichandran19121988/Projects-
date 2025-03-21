<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seeding 7 Professors
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john.doe@university.com',
                's_number' => 'L1001',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@university.com',
                's_number' => 'L1002',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@university.com',
                's_number' => 'L1003',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Lee',
                'email' => 'sarah.lee@university.com',
                's_number' => 'L1004',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@university.com',
                's_number' => 'L1005',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emily White',
                'email' => 'emily.white@university.com',
                's_number' => 'L1006',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'James Brown',
                'email' => 'james.brown@university.com',
                's_number' => 'L1007',
                'password' => bcrypt('password123'),
                'role' => 'Lecturer', 
                'profile_photo' => 'default_photo.jpg', // Assign default photo
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seeding 50 Students
        $students = [];
        $groupIds = DB::table('groups')->pluck('id')->toArray();  
        for ($i = 1; $i <= 50; $i++) {
            $students[] = [
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@student.com',
                's_number' => 'S' . str_pad($i, 7, '0', STR_PAD_LEFT), // Generate student number S0000001, S0000002, etc.
                'password' => bcrypt('password123'),
                'role' => 'student',
                'profile_photo' => 'default_photo.jpg', // Assign default photo for students as well
                'group_id' => !empty($groupIds) ? $groupIds[array_rand($groupIds)] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($students);  // Insert all students in a single query
        $this->command->info('50 students seeded successfully.');
    }
}
