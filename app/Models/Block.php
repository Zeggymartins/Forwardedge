<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'page_id',
        'parent_id',
        'type',
        'variant',
        'data',
        'order',
        'is_published',
        'visibility'
    ];
    protected $casts = ['data' => 'array', 'is_published' => 'boolean'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function children()
    {
        return $this->hasMany(Block::class, 'parent_id')->orderBy('order');
    }
}
