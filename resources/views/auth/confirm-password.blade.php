<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">{{ __('Confirm access') }}</h2>
        <p class="auth-subtitle">
            {{ __('This area is protected. Confirm your password to continue.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
