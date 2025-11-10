<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailContact extends Model
{
    protected $fillable = [
        'email',
        'name',
        'source',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower(trim((string) $value));
    }
}
