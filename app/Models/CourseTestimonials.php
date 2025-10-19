<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CourseTestimonials extends Model
{
     protected $fillable = [
        'course_id',
        'name',
        'organization',
        'image',
        'body',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
