<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseContentAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_content_id',
        'email',
        'provider',
        'status',
        'message',
    ];

    public function content()
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }
}
