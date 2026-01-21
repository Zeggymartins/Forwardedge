<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class IdentityVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * Seconds to wait before retrying after failure.
     */
    public int $backoff = 300; // 5 minutes between retries

    public function __construct(
        public User $user,
        public string $type // 'link', 'verified', 'resubmit'
    ) {
        $this->type = strtolower($type);
    }

    public function middleware(): array
    {
        return [new RateLimited('mail')];
    }

    public function build(): self
    {
        $subject = match ($this->type) {
            'link' => 'Complete Your Forward Edge Identity Verification',
            'verified' => 'Your Forward Edge Identity Verification is Complete',
            'resubmit' => 'Action Required: Update Your Verification Details',
            default => 'Forward Edge Identity Verification',
        };

        return $this->subject($subject)
            ->view('emails.scholarship.verification');
    }
}
