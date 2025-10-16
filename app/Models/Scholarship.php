<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'course_id',
        'slug',
        'status',
        'headline',
        'subtext',
        'image',
        'text',
        'cta_url',
        'about',
        'program_includes',
        'who_can_apply',
        'how_to_apply',
        'important_note',
        'closing_headline',
        'closing_cta_text',
        'closing_cta_url',
        'opens_at',
        'closes_at',
    ];

    protected $casts = [
        'program_includes' => 'array',
        'who_can_apply'    => 'array',
        'how_to_apply'     => 'array',
        'opens_at'         => 'date',
        'closes_at'        => 'date',
    ];

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
