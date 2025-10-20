<?php

namespace App\Mail;

use App\Models\MessageReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReplyToMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MessageReply $reply) {}

    public function build()
    {
        return $this->subject($this->reply->subject ?: 'Response from our team')
            ->view('emails.messages.reply')
            ->with(['reply' => $this->reply]);
    }
}