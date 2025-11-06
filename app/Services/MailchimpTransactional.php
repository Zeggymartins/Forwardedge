<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailchimpTransactional
{
    protected const BASE_URL = 'https://mandrillapp.com/api/1.0';

    public static function sendMessage(array $message): array
    {
        return static::post('/messages/send.json', ['message' => $message]);
    }

    public static function sendTemplate(string $template, array $message, array $content = []): array
    {
        return static::post('/messages/send-template.json', [
            'template_name'    => $template,
            'template_content' => $content,
            'message'          => $message,
        ]);
    }

    protected static function post(string $uri, array $payload): array
    {
        $key = config('services.mailchimp.transactional_key');
        if (blank($key)) {
            throw new \RuntimeException('Mailchimp transactional key is missing.');
        }

        $response = Http::asJson()
            ->timeout(10)
            ->post(static::BASE_URL . $uri, array_merge(['key' => $key], $payload));

        if ($response->failed()) {
            Log::error('Mailchimp transactional request failed', [
                'uri'      => $uri,
                'payload'  => $payload,
                'response' => $response->body(),
            ]);
            $response->throw();
        }

        return $response->json() ?? [];
    }
}
