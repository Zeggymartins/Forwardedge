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

        <link rel="stylesheet" href="{{ asset('frontend/assets/css/auth-fallback.css') }}">
    </head>
    <body class="auth-shell">
        <div class="auth-bg">
            <main class="auth-wrapper">
                <div class="auth-panel">

                    {{-- LEFT: Brand panel --}}
                    <aside class="auth-brand">
                        <div class="auth-logo">
                            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge Logo">
                            <span>Forward Edge</span>
                        </div>
                        <h1 class="auth-brand-title">The Management Platform</h1>
                        <p class="auth-brand-copy">
                            Manage courses, events, enrollments, and your entire website — all from one control hub.
                        </p>
                        <div class="auth-brand-badges">
                            <span>Course Builder</span>
                            <span>Page Builder</span>
                            <span>Event Manager</span>
                            <span>Enrollments</span>
                            <span>Analytics</span>
                        </div>
                    </aside>

                    {{-- RIGHT: Auth form --}}
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
