<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailchimpWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Mailchimp marketing webhooks don't include a signature by default.
        // Optionally restrict by IP or add a shared secret via query string.
        Log::info('Mailchimp Marketing Webhook', $request->all());

        $type  = $request->input('type');        // e.g. "unsubscribe", "subscribe"
        $email = strtolower($request->input('data.email') ?? '');

        // TODO: update your local DB based on $type + $email
        // e.g., mark user as unsubscribed

        return response()->noContent();
    }
}
