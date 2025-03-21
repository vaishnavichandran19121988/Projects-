<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'role', // main or assistant
    ];

    // Each CourseTeacher belongs to a Lecturer (User)
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Each CourseTeacher belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
