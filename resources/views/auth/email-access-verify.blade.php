<x-guest-layout>
    <div class="auth-header">
        <h1 class="auth-title">Confirm it’s you</h1>
        <p class="auth-subtitle">
            We detected a new device for this course access link. Enter the OTP sent to your email to continue.
        </p>
    </div>

    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="auth-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('email.access.token.sendOtp', ['token' => $token]) }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="email">Email</label>
            <input id="email" type="email" class="auth-input" value="{{ $email }}" disabled>
        </div>
        <button type="submit" class="auth-button">Send OTP</button>
    </form>

    <form method="POST" action="{{ route('email.access.token.verifyOtp', ['token' => $token]) }}" class="auth-form" style="margin-top: 1.25rem;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="auth-field">
            <label class="auth-label" for="otp">OTP Code</label>
            <input id="otp" name="otp" type="text" class="auth-input" placeholder="Enter the 6-digit code" required>
        </div>
        <button type="submit" class="auth-button">Verify & Continue</button>
    </form>
</x-guest-layout>
