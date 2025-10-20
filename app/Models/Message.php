<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_id',
        'message',
        'read_at'
    ];
    protected $casts = ['read_at' => 'datetime'];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
