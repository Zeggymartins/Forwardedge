<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'thumbnail',
        'banner_image',
        'location',
        'venue',
        'start_date',
        'end_date',
        'timezone',
        'status',
        'type',
        'price',
        'max_attendees',
        'current_attendees',
        'organizer_name',
        'organizer_email',
        'contact_phone',
        'social_links',
        'meta_description',
        'meta_keywords',
        'is_featured'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'social_links' => 'array',
        'price' => 'decimal:2',
        'is_featured' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }


    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return $this->price ? '$' . number_format($this->price, 2) : 'Free';
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    public function page()
    {
        return $this->morphOne(Page::class, 'pageable');
    }
}
