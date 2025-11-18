<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function contentArray(): array
    {
        if ($this->type !== 'list') {
            return [];
        }

        $decoded = json_decode($this->attributes['content'] ?? '', true);
        return is_array($decoded) ? array_values(array_filter($decoded, fn($item) => filled($item))) : [];
    }

    public function contentString(): string
    {
        $value = $this->attributes['content'] ?? '';

        if ($this->type === 'list') {
            return implode("\n", $this->contentArray());
        }

        return is_string($value) ? $value : (string) $value;
    }

    public function imageUrl(): ?string
    {
        if ($this->type !== 'image' || empty($this->attributes['content'])) {
            return null;
        }

        return Storage::url($this->attributes['content']);
    }

    public function quoteAuthor(): string
    {
        return $this->extras['author'] ?? $this->extras['cite'] ?? 'Forward Edge Consulting';
    }
}
