<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AjaxAuthController extends Controller
{
    // QUICK REGISTER
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string',
            'password' => 'required|string|min:6|confirmed', // confirm field required
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'user',
        ]);

        Auth::login($user, true);

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful',
            'user'    => ['id' => $user->id, 'name' => $user->name],
        ]);
    }


    // LOGIN WITH PASSWORD
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'] ?? false)) {
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }


    // SEND OTP
    public function sendOtp(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'No account found'], 404);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        // send OTP via email
        Mail::raw("Your login OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        return response()->json(['status' => 'success', 'message' => 'OTP sent to email']);
    }

    // VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $data['email'])
            ->where('otp_code', $data['otp'])
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 401);
        }

        // clear OTP
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user, true);

        return response()->json(['status' => 'success', 'message' => 'Logged in with OTP']);
    }
}
