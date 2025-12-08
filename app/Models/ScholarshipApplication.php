<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    protected $fillable = [
        'course_id',
        'course_schedule_id',
        'user_id',
        'status',
        'form_data',
        'score',
        'auto_decision',
        'decision_notes',
        'admin_notes',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'form_data'  => 'array',
        'score'      => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
