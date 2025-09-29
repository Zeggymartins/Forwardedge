<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'type',
        'content',
        'position'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // 🔥 Automatically cast JSON content to array when retrieving
    protected $casts = [
        'content' => 'array',
    ];

    public function setContentAttribute($value)
    {
        // If array → store as JSON
        if (is_array($value)) {
            $this->attributes['content'] = json_encode($value);
        } else {
            // If string, just store raw
            $this->attributes['content'] = $value;
        }
    }

    public function getContentAttribute($value)
    {
        $decoded = json_decode($value, true);

        return $decoded === null ? $value : $decoded;
    }
}
