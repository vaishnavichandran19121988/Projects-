<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        's_number', // Include s_number for mass assignment
        'role', // Include role for mass assignment
        'profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Accessor for the 'profile_photo' attribute
    public function getProfilePhotoAttribute($value)
    {
        return $value ? $value : 'default_photo.jpg';
    }

    /**
     * Relationship for enrolled courses (as a student)
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id');
    }

    /**
     * Relationship for taught courses (as a teacher or assistant)
     */
    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class, 'course_teacher', 'user_id', 'course_id')
                    ->withPivot('role'); // Ensure we can access the 'role' field in the pivot table
    }

    /**
     * Relationship for reviews written by this user
     */
    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Courses where the user is the main lecturer
     */
    public function mainCourses()
    {
        return $this->belongsToMany(Course::class, 'course_teacher', 'user_id', 'course_id')
                    ->wherePivot('role', 'main')
                    ->withPivot('role');
    }

    /**
     * Courses where the user is an assistant teacher
     */
    public function assistantCourses()
    {
        return $this->belongsToMany(Course::class, 'course_teacher', 'user_id', 'course_id')
                    ->wherePivot('role', 'assistant')
                    ->withPivot('role');
    }

    /**
     * Relationship for submissions made by the user
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    

    /**
     * Get group members for the current user's group(s) and course
     */
    public function groups()
{
    return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
}


}


