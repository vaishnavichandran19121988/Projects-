<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model


{

    protected $fillable = [
        'assessment_id',
        'user_id',
        'grade',
        'submitted_at'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

   // A submission is made by a user (reviewer)
   public function reviewer()
   {
       return $this->belongsTo(User::class, 'reviewer_id');
   }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
