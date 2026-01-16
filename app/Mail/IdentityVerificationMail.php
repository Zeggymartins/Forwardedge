<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IdentityVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $type // 'link', 'approved', 'rejected'
    ) {
        $this->type = strtolower($type);
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
