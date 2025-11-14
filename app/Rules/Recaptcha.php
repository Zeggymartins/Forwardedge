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
            $fail('Captcha verification failed, please try again.');
            return;
        }

        if ($this->action && isset($payload['action']) && $payload['action'] !== $this->action) {
            $fail('Captcha verification failed, please try again.');
        }
    }
}
