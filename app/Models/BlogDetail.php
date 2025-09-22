<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogDetail extends Model
{
     protected $fillable = ['blog_id', 'type', 'content', 'extras', 'order'];

    protected $casts = [
        'extras' => 'array',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
