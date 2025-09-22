<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'content',
        'position',
        'sort_order'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getDecodedContentAttribute()
    {
        if (in_array($this->type, ['list', 'feature', 'speaker', 'ticket', 'sponsor'])) {
            return json_decode($this->content, true);
        }
        return $this->content;
    }
}
