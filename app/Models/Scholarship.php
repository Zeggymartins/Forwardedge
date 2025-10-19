<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Scholarship extends Model
{
    protected $fillable = [
        'course_id',
        'slug',
        'status',
        'headline',
        'subtext',
        'image',              // store hero image here
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
        'opens_at'         => 'datetime',
        'closes_at'        => 'datetime',
    ];

    // Scopes
    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Accessors
    public function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = $this->image;
            if (!$path) return null;

            return Str::startsWith($path, ['http://', 'https://', '/'])
                ? $path
                : asset('storage/' . $path);
        });
    }

    public function closingUrl(): Attribute
    {
        return Attribute::get(function () {
            // prefer closing_cta_url; fall back to cta_url; else contact
            return $this->closing_cta_url ?: ($this->cta_url ?: url('contact'));
        });
    }
}
