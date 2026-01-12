<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">{{ __('Welcome back') }}</h2>
        <p class="auth-subtitle">{{ __('Sign in to continue your scholarship journey.') }}</p>
    </div>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="auth-row">
            <label class="auth-checkbox">
                <input type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('Sign in') }}
        </x-primary-button>

        <div class="auth-footer">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}">{{ __('Create one') }}</a>
        </div>
    </form>
</x-guest-layout>
