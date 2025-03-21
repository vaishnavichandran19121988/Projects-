<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['submission_id', 'reviewer_id', 'reviewee_id', 'comments', 'score', 'assessment_id'];

    // A review belongs to a submission
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // A review is written by a user (reviewer)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // A review is for a specific assessment
    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    // A review is for a specific reviewee
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function comments()
{
    return $this->hasMany(Comment::class,'review_id');
}

public function ratings()
{
    return $this->hasMany(Rating::class);
}

public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
}
}
