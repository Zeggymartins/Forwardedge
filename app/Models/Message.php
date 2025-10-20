<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }
    public function scopeRead($q)
    {
        return $q->whereNotNull('read_at');
    }
}
