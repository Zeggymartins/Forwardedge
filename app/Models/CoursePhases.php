<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePhases extends Model
{
    protected $fillable = [
        'course_content_id',
        'title',
        'order',
        'duration',
        'content',
        'image'
    ];

    protected $casts = [
        'order' => 'integer',
        'duration' => 'integer',
    ];

    // Default values for nullable fields
    protected $attributes = [
        'image' => null,
        'content' => null,
        'duration' => null,
    ];

    public function content()   // parent content
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }

    public function topics()
    {
        return $this->hasMany(CourseTopics::class, 'course_phase_id')->orderBy('order');
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('frontend/assets/images/default-phase.jpg'); // Default image
    }

    // Scope for ordering phases
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
