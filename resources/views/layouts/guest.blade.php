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

        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true)
                : null;
            $cssAsset = $manifest['resources/css/app.css']['file'] ?? null;
            $jsAsset = $manifest['resources/js/app.js']['file'] ?? null;
            $useVite = !$cssAsset && app()->environment('local');
        @endphp

        @if ($cssAsset)
            <link rel="stylesheet" href="{{ asset('build/' . $cssAsset) }}">
        @elseif ($useVite)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('frontend/assets/css/auth-fallback.css') }}">
        @endif

        <style>
            .auth-panel--single {
                grid-template-columns: minmax(0, 1fr);
                max-width: 520px;
                margin: 0 auto;
            }

            .auth-panel--single .auth-card {
                width: 100%;
            }

            .auth-panel--single .auth-header {
                text-align: center;
            }

            .auth-logo-mark {
                display: flex;
                justify-content: center;
                margin-bottom: 0.75rem;
            }

            .auth-logo-mark img {
                width: 64px;
                height: 64px;
                object-fit: contain;
            }

            @media (max-width: 640px) {
                .auth-panel--single {
                    max-width: 100%;
                }
            }
        </style>
    </head>
    <body class="auth-shell">
        <div class="auth-bg">
            <main class="auth-wrapper">
                <div class="auth-panel auth-panel--single">
                    <section class="auth-card">
                        <div class="auth-card-inner">
                            <div class="auth-logo-mark">
                                <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge Logo">
                            </div>
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </main>
        </div>

        @if ($jsAsset)
            <script type="module" src="{{ asset('build/' . $jsAsset) }}"></script>
        @endif
    </body>
</html>
