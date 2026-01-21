<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class ScholarshipApologyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ?string $recipientName = null
    ) {
    }

    public function middleware(): array
    {
        return [new RateLimited('mail')];
    }

    public function build(): self
    {
        return $this->subject('Correction: Your scholarship application is still under review')
            ->view('emails.scholarship.apology');
    }
}
