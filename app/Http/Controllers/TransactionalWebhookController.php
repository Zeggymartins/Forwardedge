<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Mandrill posts 'mandrill_events' as a JSON string
        $json = $request->input('mandrill_events', '[]');
        $events = json_decode($json, true) ?: [];

        foreach ($events as $ev) {
            $event   = $ev['event'] ?? null;          // "send","open","click","hard_bounce","reject","spam"
            $email   = strtolower($ev['msg']['email'] ?? '');
            $reason  = $ev['msg']['diag'] ?? ($ev['reject'] ?? null);

            Log::info('Mandrill Event', compact('event', 'email', 'reason'));

            // TODO: persist info (e.g., mark email as bounced/rejected)
        }

        return response()->noContent();
    }
}
