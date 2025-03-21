<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',   // Corresponds to 'course_code' in the schema
        'course_name',   // Corresponds to 'course_name' in the schema
    ];

    // Relationship for enrolled students (student role)

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->where('role', 'student');
    }

    // Relationship for the main lecturer
    public function mainLecturer()
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id')
            ->wherePivot('role', 'main')
            ->withPivot('role');  // Ensure we can access 'role' in the pivot table
    }

    // Relationship for assistant lecturers
    public function assistantLecturers()
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id')
            ->wherePivot('role', 'assistant')
            ->withPivot('role');  // Ensure we can access 'role' in the pivot table
    }
    // Relationship for assessments tied to the course
    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'course_id');
    }
}
