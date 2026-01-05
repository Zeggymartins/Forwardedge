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
        'expires_at',
        'last_accessed_at',
        'access_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'access_count' => 'integer',
    ];

    public function content()
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }
}
