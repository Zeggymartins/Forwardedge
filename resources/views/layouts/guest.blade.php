<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-shell">
        <div class="auth-bg">
            <main class="auth-wrapper">
                <div class="auth-panel">
                    <section class="auth-brand">
                        <a class="auth-logo" href="/">
                            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge">
                            <span>Forward Edge</span>
                        </a>
                        <h1 class="auth-brand-title">Secure your next career leap.</h1>
                        <p class="auth-brand-copy">
                            Access scholarships, labs, and cohorts built for serious cybersecurity growth.
                        </p>
                        <div class="auth-brand-badges">
                            <span>Scholarship-ready</span>
                            <span>Hands-on labs</span>
                            <span>Mentor support</span>
                        </div>
                    </section>

                    <section class="auth-card">
                        <div class="auth-card-inner">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
