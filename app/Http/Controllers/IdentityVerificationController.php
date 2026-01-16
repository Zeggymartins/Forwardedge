<?php

namespace App\Http\Controllers;

use App\Mail\IdentityVerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

        // If already submitted/verified, show appropriate message
        if (in_array($user->verification_status, ['pending', 'verified'])) {
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
            'id_type' => ['required', Rule::in(['nin', 'voters_card', 'drivers_license', 'intl_passport'])],
            'id_number' => 'required|string|max:50',
            'id_front' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'legal_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'state_of_origin' => 'nullable|string|max:100',
        ]);

        // Store files in private storage
        $basePath = "verifications/{$user->id}";

        $photoPath = $request->file('photo')->store($basePath, 'private');
        $idFrontPath = $request->file('id_front')->store($basePath, 'private');
        $idBackPath = $request->hasFile('id_back')
            ? $request->file('id_back')->store($basePath, 'private')
            : null;

        // Update user with verification data
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
            'verification_status' => 'pending',
        ]);

        $reasons = $this->runAutoChecks($request, $validated, $user);

        if (empty($reasons)) {
            if (!$user->enrollment_id) {
                $user->enrollment_id = User::generateEnrollmentId();
            }

            $user->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verification_notes' => null,
                'verification_token_expires_at' => now()->addDays(7),
            ]);

            Mail::to($user->email)->send(new IdentityVerificationMail($user, 'verified'));

            return redirect()->route('verify.show', $token)
                ->with('success', 'Verification complete. You can now access your course content.');
        }

        $user->update([
            'verification_status' => 'rejected',
            'verification_notes' => implode(' ', $reasons),
            'verified_at' => null,
            'verification_token_expires_at' => now()->addDays(7),
        ]);

        Mail::to($user->email)->send(new IdentityVerificationMail($user, 'resubmit'));

        return redirect()->route('verify.show', $token)
            ->with('error', 'We need a quick update to complete your verification. Please check the notes and resubmit.');
    }

    /**
     * Generate a verification token and send email to user
     */
    public static function sendVerificationEmail(User $user): void
    {
        $user->update([
            'verification_token' => Str::random(64),
            'verification_token_expires_at' => now()->addDays(7),
            'verification_status' => 'unverified',
        ]);

        Mail::to($user->email)->send(new IdentityVerificationMail($user, 'link'));
    }

    private function runAutoChecks(Request $request, array $data, User $user): array
    {
        $reasons = [];

        $legalName = $this->normalizeName($data['legal_name'] ?? '');
        $accountName = $this->normalizeName($user->name ?? '');

        if (count($legalName) < 2) {
            $reasons[] = 'Please enter your full legal name (first and last name).';
        }

        if ($legalName && $accountName && !$this->namesOverlap($legalName, $accountName)) {
            $reasons[] = 'Your legal name should match the name on your account.';
        }

        if (!$this->idNumberLooksValid($data['id_type'] ?? '', $data['id_number'] ?? '')) {
            $reasons[] = 'Please double-check the ID number format for the selected ID type.';
        }

        if (($data['id_type'] ?? '') !== 'intl_passport' && !$request->hasFile('id_back')) {
            $reasons[] = 'Please upload the back of your ID card.';
        }

        $photo = $request->file('photo');
        if ($this->imageTooSmall($photo, 300, 300)) {
            $reasons[] = 'Your photo is too small or unclear. Please upload a clearer image.';
        }

        $idFront = $request->file('id_front');
        if ($this->imageTooSmall($idFront, 600, 350)) {
            $reasons[] = 'Your ID front image is too small or unclear. Please upload a clearer image.';
        }

        $idBack = $request->file('id_back');
        if ($idBack && $this->imageTooSmall($idBack, 600, 350)) {
            $reasons[] = 'Your ID back image is too small or unclear. Please upload a clearer image.';
        }

        $dob = Carbon::parse($data['date_of_birth']);
        if ($dob->diffInYears(now()) < 13) {
            $reasons[] = 'Please confirm your date of birth.';
        }

        if (!empty($reasons)) {
            Log::info('Verification auto-check flagged', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reasons' => $reasons,
            ]);
        }

        return $reasons;
    }

    private function normalizeName(string $value): array
    {
        $value = trim(strtolower($value));
        $value = preg_replace('/[^a-z\s]/', ' ', $value);
        $parts = preg_split('/\s+/', $value);
        return array_values(array_filter($parts));
    }

    private function namesOverlap(array $legalName, array $accountName): bool
    {
        $legal = array_unique($legalName);
        $account = array_unique($accountName);

        foreach ($legal as $part) {
            if (strlen($part) >= 3 && in_array($part, $account, true)) {
                return true;
            }
        }

        return false;
    }

    private function idNumberLooksValid(string $type, string $number): bool
    {
        $number = trim($number);
        return match ($type) {
            'nin' => (bool) preg_match('/^\d{11}$/', $number),
            'voters_card' => (bool) preg_match('/^[a-zA-Z0-9]{8,20}$/', $number),
            'drivers_license' => (bool) preg_match('/^[a-zA-Z0-9]{6,15}$/', $number),
            'intl_passport' => (bool) preg_match('/^[a-zA-Z0-9]{6,12}$/', $number),
            default => false,
        };
    }

    private function imageTooSmall(?UploadedFile $file, int $minWidth, int $minHeight): bool
    {
        if (!$file) {
            return false;
        }

        if ($file->getClientOriginalExtension() === 'pdf') {
            return false;
        }

        $info = @getimagesize($file->getRealPath());
        if (!$info || !isset($info[0], $info[1])) {
            return true;
        }

        return $info[0] < $minWidth || $info[1] < $minHeight;
    }
}
