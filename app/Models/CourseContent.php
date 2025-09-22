<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'type',
        'content',
        'position',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
