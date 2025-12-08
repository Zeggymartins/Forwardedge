<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    public function __construct(protected string $action = 'form')
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret');

        if (!$secret) {
            // Skip validation if not configured; useful for local environments.
            return;
        }

        if (blank($value)) {
            $fail('Captcha verification failed, please try again.');
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::error('recaptcha.request_failed', ['message' => $e->getMessage()]);
            $fail('Captcha verification failed, please refresh and try again.');
            return;
        }

        if (!$response->successful()) {
            Log::warning('recaptcha.unsuccessful_response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            $fail('Captcha verification failed, please refresh and try again.');
            return;
        }

        $payload = $response->json();

        if (empty($payload['success'])) {
            $fail('Captcha verification failed, please refresh and try again.');
            return;
        }

        $minScore = (float) config('services.recaptcha.score', 0.5);
        $score    = (float) ($payload['score'] ?? 0);

        if ($score < $minScore) {
            Log::warning('recaptcha.low_score', [
                'score' => $score,
                'threshold' => $minScore,
                'action' => $payload['action'] ?? null,
            ]);
            $fail('Captcha verification failed, please try again.');
            return;
        }

        $allowedHosts = array_filter((array) config('services.recaptcha.allowed_hostnames', []));
        if (!empty($allowedHosts)) {
            $hostname = strtolower((string) ($payload['hostname'] ?? ''));
            $allowed = collect($allowedHosts)->map(fn ($host) => strtolower($host))->contains($hostname);

            if (!$hostname || !$allowed) {
                Log::warning('recaptcha.hostname_mismatch', [
                    'hostname' => $hostname,
                    'allowed'  => $allowedHosts,
                ]);
                $fail('Captcha verification failed, please try again.');
                return;
            }
        }

        if ($this->action && isset($payload['action']) && $payload['action'] !== $this->action) {
            Log::warning('recaptcha.action_mismatch', [
                'expected' => $this->action,
                'received' => $payload['action'],
            ]);
            $fail('Captcha verification failed, please try again.');
        }
    }
}
