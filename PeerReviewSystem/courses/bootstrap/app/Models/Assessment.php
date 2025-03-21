<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'instruction',
        'number_of_reviews',
        'max_score',
        'due_date',
        'type',
        'course_id',
        'file_path' 
    ];

    public function course()
    {
        return $this->belongsTo(Course::class,'course_id');
    }

    // Relationship: One assessment can have many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'assessment_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
