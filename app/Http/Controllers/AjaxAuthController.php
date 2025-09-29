<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Mail,
    RateLimiter,
    Password
};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\OtpMail;

class AjaxAuthController extends Controller
{
    // QUICK REGISTER
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'    => ['nullable', 'string', 'max:50', Rule::unique('users', 'phone')],
            'address'  => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'address'           => $data['address'] ?? null,
            'password'          => Hash::make($data['password']),
            'role'              => 'user',
            'email_verified_at' => null, // require email verification
        ]);

        // Send email verification link
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful. Please check your email for verification.',
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        // throttle key (email+ip)
        $key = Str::lower($data['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many login attempts. Try again later.'
            ], 429);
        }

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'] ?? false)) {
            $user = Auth::user();

            // if (!$user->hasVerifiedEmail()) {
            //     Auth::logout();
            //     return response()->json(['status' => 'error', 'message' => 'Please verify your email first.'], 403);
            // }

            RateLimiter::clear($key);
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully']);
        }

        RateLimiter::hit($key, 60); // lockout for 1 minute per attempt
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    // SEND OTP (with cooldown)
    public function sendOtp(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No account found'], 404);
        }

        // cooldown 30s
        if ($user->otp_expires_at && $user->otp_expires_at->gt(now()->subSeconds(30))) {
            return response()->json(['status' => 'error', 'message' => 'Please wait before requesting another OTP.'], 429);
        }

        $otp = Str::upper(Str::random(6)); // alphanumeric
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->otp_attempts = 0; // reset attempts
        $user->save();

        Mail::to($user)->send(new OtpMail($otp));

        return response()->json(['status' => 'success', 'message' => 'OTP sent to email']);
    }

    // VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$user->otp_code || $user->otp_expires_at->lt(now())) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 401);
        }

        // attempts check
        if ($user->otp_attempts >= 5) {
            return response()->json(['status' => 'error', 'message' => 'Too many failed attempts'], 429);
        }

        if ($user->otp_code !== $data['otp']) {
            $user->increment('otp_attempts');
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP'], 401);
        }

        // success → clear otp
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->save();

        Auth::login($user, true);

        return response()->json(['status' => 'success', 'message' => 'Logged in with OTP']);
    }

    // FORGOT PASSWORD (send reset link)
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => 'success', 'message' => 'Password reset link sent'])
            : response()->json(['status' => 'error', 'message' => 'Unable to send reset link'], 500);
    }

    // RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => 'success', 'message' => 'Password reset successful'])
            : response()->json(['status' => 'error', 'message' => 'Password reset failed'], 500);
    }
}
