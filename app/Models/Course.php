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
        'description',
        'thumbnail',
        'status',
        'price',
        'discount_price',
        'is_featured'
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

    public function details()
    {
        return $this->hasMany(CourseDetails::class)->orderBy('sort_order', 'asc');
    }
}
