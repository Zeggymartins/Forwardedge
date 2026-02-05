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
        'delivery_mode',
        'price',
        'discount_price',
        'content',
        'order',
        'drive_folder_id',
        'drive_share_link',
        'auto_grant_access',
    ];

    protected $casts = [
        'order' => 'integer',
        'auto_grant_access' => 'boolean',
        'price' => 'float',
        'discount_price' => 'float',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function phases()
    {
        return $this->hasMany(CoursePhases::class, 'course_content_id')->ordered();
    }

    public function reviews()
    {
        return $this->hasMany(CourseContentReview::class)->latest();
    }

    public function accessLogs()
    {
        return $this->hasMany(CourseContentAccessLog::class);
    }

    public function averageRating(): float
    {
        if ($this->relationLoaded('reviews')) {
            $avg = $this->reviews->avg('rating');
        } else {
            $avg = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');
        }

        return round((float) ($avg ?? 0), 1);
    }
}
