<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $fillable = [
        'title',
        'subject',
        'subtitle',
        'hero_image',
        'intro',
        'blocks',
        'cta_text',
        'cta_link',
        'status',
        'sent_count',
        'total_count',
        'last_error',
        'user_id',
    ];

    protected $casts = [
        'blocks' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class);
    }

    public function refreshProgress(): void
    {
        $total = $this->recipients()->count();
        $sent = $this->recipients()->where('status', 'sent')->count();
        $hasFailed = $this->recipients()->where('status', 'failed')->exists();
        $pending = $this->recipients()->whereIn('status', ['pending', 'sending'])->exists();

        $status = $hasFailed
            ? 'failed'
            : ($pending ? 'sending' : 'completed');

        $this->forceFill([
            'total_count' => $total,
            'sent_count'  => $sent,
            'status'      => $status,
            'last_error'  => $hasFailed ? $this->last_error : null,
        ])->save();
    }
}
