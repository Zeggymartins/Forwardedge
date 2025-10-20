<?php
// app/Models/MessageReply.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReply extends Model
{
    protected $fillable = [
        'message_id',
        'admin_id',
        'to_email',
        'subject',
        'body',
        'mailed_at',
    ];

    protected $casts = [
        'mailed_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }
}
