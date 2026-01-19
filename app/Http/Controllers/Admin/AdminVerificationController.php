<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IdentityVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminVerificationController extends Controller
{
    /**
     * List all identity verifications
     */
    public function index(Request $request)
    {
        $query = User::whereNotNull('verification_token')
            ->orWhereIn('verification_status', ['pending', 'verified', 'rejected']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('legal_name', 'like', "%{$search}%")
                    ->orWhere('enrollment_id', 'like', "%{$search}%");
            });
        }

        $verifications = $query->orderByRaw("FIELD(verification_status, 'pending', 'unverified', 'rejected', 'verified')")
            ->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.verifications.index', compact('verifications'));
    }

    /**
     * View a single verification
     */
    public function show(User $user)
    {
        return view('admin.pages.verifications.show', compact('user'));
    }

    /**
     * Stream a verification document securely
     */
    public function viewDocument(User $user, string $type)
    {
        $path = match ($type) {
            'photo' => $user->photo,
            'id_front' => $user->id_front,
            'id_back' => $user->id_back,
            default => null,
        };

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Document not found.');
        }

        return response()->file(Storage::disk('private')->path($path));
    }

    /**
     * Resend verification email
     */
    public function resend(User $user)
    {
        $user->update([
            'verification_token' => Str::random(64),
            'verification_token_expires_at' => now()->addDays(7),
            'verification_status' => 'unverified',
        ]);

        try {
            Mail::to($user->email)->queue(new IdentityVerificationMail($user, 'link'));
        } catch (\Exception $e) {
            \Log::error('Failed to queue resend verification email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to queue email, but token was reset.');
        }

        return back()->with('success', 'Verification email has been queued.');
    }
}
