<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail, Password, RateLimiter};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AjaxAuthController extends Controller
{
    /* ========== Helpers ========== */
    private function normalizePhone(?string $raw): ?string
    {
        if (!$raw) return null;
        $raw = trim($raw);
        $isPlus = Str::startsWith($raw, '+');
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        // Nigeria-friendly normalization
        if (!$isPlus) {
            if (Str::startsWith($digits, '0')) {
                $digits = '+234' . ltrim($digits, '0');
            } elseif (Str::startsWith($digits, '234')) {
                $digits = '+' . $digits;
            } else {
                $digits = '+' . $digits;
            }
        } else {
            $digits = '+' . $digits;
        }
        return $digits;
    }

    private function throttleKey(string $identifier, Request $request): string
    {
        return Str::lower($identifier) . '|' . $request->ip();
    }

    /* ========== REGISTER (password = phone) ========== */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'   => ['required', 'string', 'max:50'],
            'address' => 'nullable|string|max:500',
        ]);

        $phone = $this->normalizePhone($data['phone']);

        if (User::where('phone', $phone)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Phone number already in use.',
                'errors'  => ['phone' => ['The phone has already been taken.']],
            ], 422);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $phone,
            'address'  => $data['address'] ?? null,
            'password' => Hash::make($phone), // initial password = phone
            'role'     => 'user',
            'email_verified_at' => null,
            'must_change_password' => true,   // optional: force change on first login
        ]);

        // Optional: email verification
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful. Your initial password is your phone number. Please check your email for verification.',
        ]);
    }

    /* ========== LOGIN (email + password) ========== */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $key = $this->throttleKey($data['email'], $request);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['status' => 'error', 'message' => 'Too many login attempts. Try again later.'], 429);
        }

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], (bool)($data['remember'] ?? false))) {
            RateLimiter::clear($key);

            // Optional: block until email verified
            // if (!Auth::user()->hasVerifiedEmail()) { Auth::logout(); return response()->json(['status'=>'error','message'=>'Please verify your email first.'], 403); }

            return response()->json([
                'status'  => 'success',
                'message' => 'Logged in successfully',
                'must_change_password' => (bool) Auth::user()->must_change_password, // tell frontend to prompt change
            ]);
        }

        RateLimiter::hit($key, 60);
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    /* ========== SEND OTP (email) — stores HASH, not raw; throttled ========== */
    public function sendOtp(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No account found'], 404);
        }

        // 30s cooldown between sends
        if ($user->otp_expires_at && $user->otp_expires_at->gt(now()->subSeconds(30))) {
            return response()->json(['status' => 'error', 'message' => 'Please wait before requesting another OTP.'], 429);
        }

        // Generate numeric OTP (easier to type); you can keep alphanumeric if you want
        $otp = (string) random_int(100000, 999999);

        // Store HASH, never the raw code
        $user->otp_code_hash = Hash::make($otp);
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->otp_attempts = 0;
        $user->save();

        // Send via mail (queue recommended)
        Mail::to($user)->send(new OtpMail($otp));

        // Never log or return the OTP
        return response()->json(['status' => 'success', 'message' => 'OTP sent to email']);
    }

    /* ========== VERIFY OTP — compares against HASH ========== */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !$user->otp_code_hash || !$user->otp_expires_at || $user->otp_expires_at->lt(now())) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 401);
        }

        if ($user->otp_attempts >= 5) {
            return response()->json(['status' => 'error', 'message' => 'Too many failed attempts'], 429);
        }

        if (!Hash::check($data['otp'], $user->otp_code_hash)) {
            $user->increment('otp_attempts');
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP'], 401);
        }

        // success → clear OTP
        $user->otp_code_hash = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->save();

        Auth::login($user, true);

        return response()->json(['status' => 'success', 'message' => 'Logged in with OTP']);
    }

    /* ========== FORGOT / RESET (Laravel broker) ========== */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => 'success', 'message' => 'Password reset link sent. Please check your email.'])
            : response()->json(['status' => 'error', 'message' => 'Unable to send reset link'], 500);
    }

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
                    'must_change_password' => false,
                ])->save();
                $user->setRememberToken(Str::random(60));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => 'success', 'message' => 'Password reset successful'])
            : response()->json(['status' => 'error', 'message' => 'Password reset failed'], 500);
    }

    /* ========== CHANGE PASSWORD (authenticated) ========== */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:6|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect'], 422);
        }

        $user->password = Hash::make($data['new_password']);
        $user->must_change_password = false;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password updated']);
    }

    /* ========== LOGOUT (AJAX) ========== */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success']);
    }
}
