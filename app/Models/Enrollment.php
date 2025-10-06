<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_schedule_id',
        'payment_plan',
        'total_amount',
        'balance',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseSchedule()
    {
        return $this->belongsTo(CourseSchedule::class);
    }
}
