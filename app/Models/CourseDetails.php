<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDetails extends Model
{
    protected $fillable = ['course_id', 'type', 'content', 'image', 'sort_order'];

    protected $casts = [
        'content' => 'array', // useful if storing JSON for features/lists
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
