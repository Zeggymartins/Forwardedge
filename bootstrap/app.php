<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Safe environment check at bootstrap time (no container usage)
        $appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production';

        // Only enable the logger in local (or comment this if you want it always)
        if ($appEnv === 'local') {
            // Put CSRF logger BEFORE VerifyCsrfToken in the 'web' stack
            $middleware->prependToGroup('web', \App\Http\Middleware\LogCsrfFailures::class);
        }

        // Existing aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
