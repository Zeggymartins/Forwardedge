<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\EmailAccessToken;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class EmailAccessController extends Controller
{
    /**
     * Signed email link for course access.
     * Issues a device-bound token and forwards to the token endpoint.
     */
    public function course(Request $request, Course $course, User $user)
    {
        if ($user->role !== 'user') {
            abort(403, 'Unauthorized');
        }

        $this->ensureCourseAccess($user, $course);

        $token = EmailAccessToken::issueForCourse(
            $user,
            $course,
            now()->addHours(72)
        );

        return redirect()->route('email.access.token', ['token' => $token]);
    }

    /**
     * Device-bound access token for course links sent by email.
     */
    public function token(Request $request, string $token)
    {
        $emailToken = EmailAccessToken::where('token_hash', hash('sha256', $token))->firstOrFail();

        if ($emailToken->expires_at->isPast()) {
            abort(403, 'This access link has expired.');
        }

        $user = $emailToken->user;
        $course = $emailToken->course;

        if (!$user || !$course || $user->role !== 'user') {
            abort(403, 'Unauthorized');
        }

        $this->ensureCourseAccess($user, $course);

        $ip = (string) $request->ip();
        $userAgent = (string) $request->userAgent();
        $userAgentHash = hash('sha256', $userAgent);

        if (!$emailToken->first_ip || !$emailToken->first_user_agent_hash) {
            $emailToken->first_ip = $ip;
            $emailToken->first_user_agent_hash = $userAgentHash;
            $emailToken->used_at = now();
        } elseif (
            !hash_equals($emailToken->first_ip, $ip) ||
            !hash_equals($emailToken->first_user_agent_hash, $userAgentHash)
        ) {
            Log::warning('Email access blocked (device mismatch)', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'email' => $user->email,
                'token_id' => $emailToken->id,
                'ip' => $ip,
            ]);

            return view('auth.email-access-verify', [
                'token' => $token,
                'email' => $user->email,
                'course' => $course,
            ]);
        }

        $emailToken->last_used_at = now();
        $emailToken->save();

        if (Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        Log::info('Email access granted', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'email' => $user->email,
            'ip' => $ip,
        ]);

        return redirect()->route('student.courses.content', $course->id);
    }

    public function sendOtp(Request $request, string $token)
    {
        $emailToken = EmailAccessToken::where('token_hash', hash('sha256', $token))->firstOrFail();

        if ($emailToken->expires_at->isPast()) {
            return back()->withErrors(['otp' => 'This access link has expired.']);
        }

        $user = $emailToken->user;
        if (!$user || $user->role !== 'user') {
            return back()->withErrors(['otp' => 'Unable to send OTP.']);
        }

        if (!$this->sendOtpForUser($user)) {
            return back()->withErrors(['otp' => 'Please wait before requesting another OTP.']);
        }

        return back()->with('status', 'OTP sent to your email.');
    }

    public function verifyOtp(Request $request, string $token)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $emailToken = EmailAccessToken::where('token_hash', hash('sha256', $token))->firstOrFail();

        if ($emailToken->expires_at->isPast()) {
            return back()->withErrors(['otp' => 'This access link has expired.']);
        }

        $user = $emailToken->user;
        $course = $emailToken->course;
        if (!$user || !$course || $user->role !== 'user') {
            return back()->withErrors(['otp' => 'Unauthorized.']);
        }

        if (!hash_equals($user->email, $data['email'])) {
            return back()->withErrors(['email' => 'Email does not match this access link.']);
        }

        $this->ensureCourseAccess($user, $course);

        if (!$this->verifyOtpForUser($user, $data['otp'])) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $emailToken->first_ip = (string) $request->ip();
        $emailToken->first_user_agent_hash = hash('sha256', (string) $request->userAgent());
        $emailToken->used_at = $emailToken->used_at ?? now();
        $emailToken->last_used_at = now();
        $emailToken->save();

        if (Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        Log::info('Email access rebound after OTP', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('student.courses.content', $course->id);
    }

    private function ensureCourseAccess(User $user, Course $course): void
    {
        $hasPaidOrder = OrderItem::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'paid');
        })
            ->where('course_id', $course->id)
            ->exists();

        $hasModulePurchase = OrderItem::whereNotNull('course_content_id')
            ->whereHas('courseContent', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'paid');
            })
            ->exists();

        $hasEnrollment = Enrollment::where('user_id', $user->id)
            ->whereHas('courseSchedule', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->exists();

        if (!$hasPaidOrder && !$hasModulePurchase && !$hasEnrollment) {
            abort(403, 'Access not available for this course.');
        }
    }

    private function sendOtpForUser(User $user): bool
    {
        if ($user->otp_expires_at && $user->otp_expires_at->gt(now()->subSeconds(30))) {
            return false;
        }

        $otp = (string) random_int(100000, 999999);
        $user->otp_code_hash = Hash::make($otp);
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->otp_attempts = 0;
        $user->save();

        Mail::to($user)->send(new OtpMail($otp));

        return true;
    }

    private function verifyOtpForUser(User $user, string $otp): bool
    {
        if (!$user->otp_code_hash || !$user->otp_expires_at || $user->otp_expires_at->lt(now())) {
            return false;
        }

        if ($user->otp_attempts >= 5) {
            return false;
        }

        if (!Hash::check($otp, $user->otp_code_hash)) {
            $user->increment('otp_attempts');
            return false;
        }

        $user->otp_code_hash = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->save();

        return true;
    }
}
