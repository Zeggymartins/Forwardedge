<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class LogCsrfFailures
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            Log::warning('CSRF_FAIL', [
                'url'           => $request->fullUrl(),
                'method'        => $request->method(),
                'route'         => optional($request->route())->getName(),
                'has__token'    => $request->has('_token'),
                'input__token'  => substr((string)$request->input('_token'), 0, 12),
                'header_token'  => substr((string)$request->header('X-CSRF-TOKEN'), 0, 12),
                'session_token' => substr((string)$request->session()->token(), 0, 12),
                'cookies'       => array_keys($request->cookies->all()),
                'origin'        => $request->header('Origin'),
                'referer'       => $request->header('Referer'),
            ]);
            throw $e;
        }
    }
}
