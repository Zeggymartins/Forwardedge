<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'token_hash',
        'expires_at',
        'first_ip',
        'first_user_agent_hash',
        'used_at',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function issueForCourse(User $user, Course $course, \DateTimeInterface $expiresAt): string
    {
        self::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        $token = Str::random(64);

        self::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }
}
