<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'file_path',
        'type',
        'content',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function phases()
    {
        return $this->hasMany(CoursePhases::class, 'course_content_id')->ordered();
    }
}
