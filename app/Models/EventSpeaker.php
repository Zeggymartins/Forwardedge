<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSpeaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'title',
        'company',
        'bio',
        'image',
        'email',
        'social_links',
        'is_keynote',
        'sort_order'
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_keynote' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function schedules()
    {
        return $this->hasMany(EventSchedule::class, 'speaker_id');
    }
}
