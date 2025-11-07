<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $recipientName;

    public function __construct(string $recipientName = 'Subscriber')
    {
        $this->recipientName = $recipientName ?: 'Subscriber';
    }

    public function build(): self
    {
        return $this->subject('You\'re officially on the Forward Edge newsletter')
            ->view('emails.newsletter.welcome')
            ->with([
                'name' => $this->recipientName,
            ]);
    }
}
