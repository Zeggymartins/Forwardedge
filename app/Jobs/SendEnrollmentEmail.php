<?php

namespace App\Jobs;

use App\Mail\EnrollmentConfirmed;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SendEnrollmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $enrollment;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [60, 300];

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function handle()
    {
        try {
            Log::info("Sending enrollment email", [
                'enrollment_id' => $this->enrollment->id,
                'user_email' => $this->enrollment->user->email,
                'course' => $this->enrollment->courseSchedule->course->name
            ]);

            Mail::to($this->enrollment->user->email)
                ->send(new EnrollmentConfirmed($this->enrollment));

            Log::info("Enrollment email sent successfully", [
                'enrollment_id' => $this->enrollment->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send enrollment email", [
                'enrollment_id' => $this->enrollment->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Enrollment email job permanently failed", [
            'enrollment_id' => $this->enrollment->id,
            'user_email' => $this->enrollment->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}
