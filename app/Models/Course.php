<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'brief_description',
        'thumbnail',
        'status',
    ];
    protected $table = 'courses';

    public function contents()
    {
        return $this->hasMany(CourseContent::class)->orderBy('position', 'asc');
    }
    public function phases()
    {
        return $this->hasMany(CoursePhases::class)->orderBy('order');
    }
    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }
}
