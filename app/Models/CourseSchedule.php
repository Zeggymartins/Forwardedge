<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    protected $fillable = ['course_id', 'start_date', 'end_date', 'location', 'type', 'price'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
