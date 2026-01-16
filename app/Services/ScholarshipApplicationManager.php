<?php

namespace App\Services;

use App\Http\Controllers\IdentityVerificationController;
use App\Mail\ScholarshipStatusMail;
use App\Models\Enrollment;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Facades\Mail;

class ScholarshipApplicationManager
{
    public function approve(ScholarshipApplication $application, ?string $notifyEmail = null): ScholarshipApplication
    {
        if ($application->status !== 'approved') {
            $this->createEnrollmentIfMissing($application);

            $application->forceFill([
                'status'      => 'approved',
                'approved_at' => $application->approved_at ?? now(),
            ])->save();
        }

        $fresh = $application->fresh(['course', 'schedule', 'user']);
        $email = $notifyEmail ?: ($fresh->user?->email);

        if ($email) {
            Mail::to($email)->send(new ScholarshipStatusMail($fresh, 'approved'));
        }

        // Send identity verification email if user exists and not already verified
        if ($fresh->user && $fresh->user->verification_status !== 'verified') {
            IdentityVerificationController::sendVerificationEmail($fresh->user);
        }

        return $fresh;
    }

    protected function createEnrollmentIfMissing(ScholarshipApplication $application): void
    {
        $exists = Enrollment::where([
            'course_id'          => $application->course_id,
            'course_schedule_id' => $application->course_schedule_id,
            'user_id'            => $application->user_id,
        ])->exists();

        if ($exists) {
            return;
        }

        Enrollment::create([
            'course_id'          => $application->course_id,
            'course_schedule_id' => $application->course_schedule_id,
            'user_id'            => $application->user_id,
            'payment_plan'       => 'full',
            'total_amount'       => 0,
            'balance'            => 0,
            'status'             => 'active',
        ]);
    }
}
