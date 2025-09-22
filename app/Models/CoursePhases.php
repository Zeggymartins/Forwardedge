<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePhases extends Model
{
    protected $fillable = ['course_id', 'title', 'order', 'duration'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function topics()
    {
        return $this->hasMany(CourseTopics::class)->orderBy('order');
    }
}
