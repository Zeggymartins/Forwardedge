<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quantity_available',
        'quantity_sold',
        'sale_start',
        'sale_end',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
        'features' => 'array',
        'is_active' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'ticket_id');
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity_available ? ($this->quantity_available - $this->quantity_sold) : null;
    }

    public function getIsAvailableAttribute()
    {
        if (!$this->is_active) return false;
        if ($this->sale_start && $this->sale_start > now()) return false;
        if ($this->sale_end && $this->sale_end < now()) return false;
        if ($this->available_quantity !== null && $this->available_quantity <= 0) return false;

        return true;
    }

    public function getFeaturesAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        // If it's valid JSON, decode it
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Otherwise treat it as comma-separated
        return explode(',', $value);
    }
}
