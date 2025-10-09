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
        'message'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
