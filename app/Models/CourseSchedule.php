<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CourseSchedule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'start_date',
        'end_date',
        'location',
        'type',
        'price',
        'description',
        'tag',
        'price_usd',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Scope: only schedules in the future (or today) */
    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereDate('start_date', '>=', now()->toDateString());
    }

    /** Scope: only schedules whose course is published */
    public function scopeForPublishedCourses(Builder $q): Builder
    {
        return $q->whereHas('course', function (Builder $cq) {
            $cq->where('status', 'published');
        });
    }

    /** Optional helper: is this schedule upcoming? */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date && $this->start_date->isSameDay(now()) || $this->start_date->greaterThan(now());
    }
    public function isFree(): bool
    {
        return is_null($this->price) || (float)$this->price <= 0;
    }
}


