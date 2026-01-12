<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">{{ __('Reset your password') }}</h2>
        <p class="auth-subtitle">
            {{ __('Share your email and we will send a secure reset link.') }}
        </p>
    </div>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Send reset link') }}
        </x-primary-button>
    </form>
</x-guest-layout>
