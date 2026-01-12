<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">{{ __('Verify your email') }}</h2>
        <p class="auth-subtitle">
            {{ __('Open the link we sent to your inbox to finish setup. Need a new email? We can resend it.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-status">
            {{ __('A fresh verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="auth-row">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend verification email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-link">
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
