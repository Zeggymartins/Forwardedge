<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'schedule_date',
        'start_time',
        'end_time',
        'session_title',
        'description',
        'speaker_name',
        'speaker_id',
        'location',
        'session_type',
        'sort_order'
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function speaker()
    {
        return $this->belongsTo(EventSpeaker::class);
    }

    public function getFormattedTimeAttribute()
    {
        try {
            // Handle different possible formats
            if ($this->start_time instanceof \Carbon\Carbon) {
                $startTime = $this->start_time;
            } else {
                // Try different formats
                $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time);
                if (!$startTime) {
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
                }
            }

            if ($this->end_time instanceof \Carbon\Carbon) {
                $endTime = $this->end_time;
            } else {
                // Try different formats
                $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time);
                if (!$endTime) {
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
                }
            }

            return $startTime->format('H:i') . ' - ' . $endTime->format('H:i');
        } catch (\Exception $e) {
            // Fallback: return the raw times if formatting fails
            return $this->start_time . ' - ' . $this->end_time;
        }
    }
}
