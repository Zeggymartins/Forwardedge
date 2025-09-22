<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTopics extends Model
{
    protected $fillable = ['course_phase_id', 'title', 'content', 'order'];

    public function phase()
    {
        return $this->belongsTo(CoursePhases::class);
    }
}
