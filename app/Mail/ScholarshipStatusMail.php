<?php

namespace App\Mail;

use App\Models\ScholarshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScholarshipStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ScholarshipApplication $application,
        public string $status,
        public ?string $notes = null
    ) {
        $this->status = strtolower($status);
    }

    public function build(): self
    {
        $subject = $this->status === 'approved'
            ? '🎉 Your Forward Edge scholarship application was approved'
            : 'Update on your Forward Edge scholarship application';

        return $this->subject($subject)
            ->view('emails.scholarship.status');
    }
}
