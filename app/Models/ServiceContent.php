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

    protected $casts = [
        'content' => 'string', // always store as string in DB
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Mutator: store arrays as JSON
     */
    public function setContentAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['content'] = json_encode($value);
        } else {
            $this->attributes['content'] = $value;
        }
    }

    /**
     * Accessor: return correct type for Blade
     */
    public function getContentAttribute($value)
    {
        if (in_array($this->type, ['image', 'video'])) {
            return $value; // file path as string
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded; // for lists/features
        }

        return $value; // plain string for paragraph/heading
    }

    /**
     * 🔥 Helper: always give a safe string for Blade
     */
    public function asString()
    {
        return is_array($this->content)
            ? implode(', ', $this->content)
            : (string) $this->content;
    }

    /**
     * 🔥 Helper: always give an array (list/feature fallback)
     */
    public function asArray()
    {
        return is_array($this->content) ? $this->content : [];
    }
}
