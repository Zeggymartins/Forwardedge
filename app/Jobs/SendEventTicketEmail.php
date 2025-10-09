<?php

namespace App\Jobs;

use App\Mail\EventTicketConfirmed;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable as QueueableTrait;
use Illuminate\Queue\InteractsWithQueue;

class SendEventTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, QueueableTrait, SerializesModels;

    protected $registration;

    public $tries = 3;                 // max retry attempts
    public $timeout = 60;              // seconds before timing out
    public $backoff = [60, 300];       // wait times between retries

    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function handle()
    {
        try {
            Log::info("Sending event ticket email", [
                'registration_id' => $this->registration->id,
                'user_email' => $this->registration->email,
                'event_id' => $this->registration->event_id,
                'ticket_id' => $this->registration->ticket_id,
            ]);

            Mail::to($this->registration->email)
                ->send(new EventTicketConfirmed($this->registration));

            Log::info("Event ticket email sent successfully", [
                'registration_id' => $this->registration->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send event ticket email", [
                'registration_id' => $this->registration->id,
                'user_email' => $this->registration->email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e; // ensure Laravel retries the job
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Event ticket email job permanently failed", [
            'registration_id' => $this->registration->id,
            'user_email' => $this->registration->email,
            'error' => $exception->getMessage()
        ]);
    }
}
