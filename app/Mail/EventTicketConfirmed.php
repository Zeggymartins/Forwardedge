<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventTicketConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function build()
    {
        return $this->subject("Event Registration Confirmed")
            ->view('emails.events.confirmed')
            ->with(['registration' => $this->registration]);
    }
}