<?php

namespace App\Http\Controllers;

use App\Mail\IdentityVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IdentityVerificationController extends Controller
{
    /**
     * Show the verification form
     */
    public function show(string $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            abort(404, 'Verification link not found.');
        }

        if (!$user->hasValidVerificationToken()) {
            return view('user.pages.verify-identity-expired', compact('user'));
        }

        // If already verified, redirect to intended URL (e.g., pricing page) or show status
        if ($user->verification_status === 'verified') {
            $intended = session()->pull('url.intended');
            if ($intended) {
                return redirect($intended)
                    ->with('success', 'You are already verified! Proceed with enrollment.');
            }
            return view('user.pages.verify-identity-status', compact('user'));
        }

        // If pending, show status page
        if ($user->verification_status === 'pending') {
            return view('user.pages.verify-identity-status', compact('user'));
        }

        return view('user.pages.verify-identity', compact('user', 'token'));
    }

    /**
     * Process the verification form submission
     */
    public function store(Request $request, string $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            abort(404, 'Verification link not found.');
        }

        if (!$user->hasValidVerificationToken()) {
            return back()->with('error', 'Your verification link has expired. Please contact support for a new link.');
        }

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_type' => ['nullable', Rule::in(['nin', 'national_id', 'voters_card', 'drivers_license', 'intl_passport', 'student_id', 'work_id'])],
            'id_number' => 'nullable|string|max:50',
            'id_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'legal_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'state_of_origin' => 'nullable|string|max:100',
        ]);

        // Store files in private storage
        $basePath = "verifications/{$user->id}";

        $photoPath = $request->file('photo')->store($basePath, 'private');
        $idFrontPath = $request->hasFile('id_front')
            ? $request->file('id_front')->store($basePath, 'private')
            : null;
        $idBackPath = $request->hasFile('id_back')
            ? $request->file('id_back')->store($basePath, 'private')
            : null;

        // Generate enrollment ID if not already set
        if (!$user->enrollment_id) {
            $user->enrollment_id = User::generateEnrollmentId();
        }

        // Update user with verification data - auto-verify on submission
        $user->update([
            'photo' => $photoPath,
            'id_type' => $validated['id_type'],
            'id_number' => $validated['id_number'],
            'id_front' => $idFrontPath,
            'id_back' => $idBackPath,
            'legal_name' => $validated['legal_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'nationality' => $validated['nationality'],
            'state_of_origin' => $validated['state_of_origin'],
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verification_notes' => null,
        ]);

        // Send verification complete email
        try {
            Mail::to($user->email)->queue(new IdentityVerificationMail($user, 'verified'));
        } catch (\Exception $e) {
            Log::error('Failed to queue verification email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        // Redirect to intended URL (e.g., pricing page) if set
        $intended = session()->pull('url.intended');
        if ($intended) {
            return redirect($intended)
                ->with('success', 'Verification complete! You can now proceed with enrollment.');
        }

        return redirect()->route('verify.show', $token)
            ->with('success', 'Verification complete. You can now access your course content.');
    }

    /**
     * Generate a verification token and send email to user
     */
    public static function sendVerificationEmail(User $user): void
    {
        $user->update([
            'verification_token' => Str::random(64),
            'verification_token_expires_at' => now()->addHours(48),
            'verification_status' => 'unverified',
        ]);

        // Queue the email - will retry automatically if rate limited
        try {
            Mail::to($user->email)->queue(new IdentityVerificationMail($user, 'link'));
        } catch (\Exception $e) {
            Log::error('Failed to queue verification link email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Serve user verification photo publicly
     */
    public function photo(User $user)
    {
        if (!$user->photo || !Storage::disk('private')->exists($user->photo)) {
            // Redirect to generated avatar
            $name = urlencode($user->name ?? 'User');
            return redirect("https://ui-avatars.com/api/?name={$name}&background=e2e8f0&color=64748b&size=88");
        }

        $path = Storage::disk('private')->path($user->photo);
        $lastModified = filemtime($path);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=604800', // Cache for 7 days
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
            'ETag' => md5($user->id . $lastModified),
        ]);
    }
}
