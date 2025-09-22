<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'author_id',
        'category',
        'is_published'
    ];

    public function details()
    {
        return $this->hasMany(BlogDetail::class)->orderBy('order');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
