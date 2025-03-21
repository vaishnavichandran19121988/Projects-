<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name','assessment_id']; // You can add other fields as necessary

    /**
     * A group can have many users.
     */
    // A group belongs to many users
    public function users()
{
    return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
}

public function students()
{
    // Assuming you have a 'role' column in the users table to differentiate students
    return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
                ->where('role', 'student');
}
    
    /**
     * A group can have many submissions (through users).
     */
    public function submissions()
    {
        return $this->hasManyThrough(Submission::class, User::class);
    }

    /**
     * A group can have many reviews (through submissions).
     */
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Submission::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
