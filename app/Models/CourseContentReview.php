<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseContentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_content_id',
        'user_id',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function content()
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
