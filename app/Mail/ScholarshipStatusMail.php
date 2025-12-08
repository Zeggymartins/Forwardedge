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
        if ($this->status === 'approved') {
            $subject = '🎉 Your Forward Edge scholarship application was approved';
        } elseif ($this->status === 'rejected') {
            $subject = 'Update on your Forward Edge scholarship application';
        } else {
            $subject = 'Thanks for applying for the Forward Edge scholarship';
        }

        return $this->subject($subject)
            ->view('emails.scholarship.status');
    }
}
