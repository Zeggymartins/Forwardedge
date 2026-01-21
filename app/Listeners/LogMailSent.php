<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mime\Address;

class LogMailSent
{
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;

            $to = $this->normalizeAddresses($message->getTo());
            $from = $this->normalizeAddresses($message->getFrom());

            $messageIdHeader = $message->getHeaders()->get('Message-ID');
            $messageId = $messageIdHeader ? trim($messageIdHeader->getBodyAsString()) : null;

            $mailable = $event->data['mailable'] ?? null;
            if (is_object($mailable)) {
                $mailable = get_class($mailable);
            } elseif (!is_string($mailable)) {
                $mailable = null;
            }

            $mailer = $event->data['mailer'] ?? (config('mail.default') ?: null);

            DB::table('mail_logs')->insert([
                'mailer' => $mailer,
                'mailable' => $mailable,
                'subject' => $message->getSubject(),
                'to' => $to ? json_encode($to) : null,
                'from' => $from ? json_encode($from) : null,
                'message_id' => $messageId,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never fail the mail send if logging fails.
        }
    }

    /**
     * @param Address[]|null $addresses
     * @return array<int, array{address:string,name:string}>
     */
    private function normalizeAddresses(?array $addresses): array
    {
        if (!$addresses) {
            return [];
        }

        return collect($addresses)
            ->filter(fn ($address) => $address instanceof Address)
            ->map(fn (Address $address) => [
                'address' => $address->getAddress(),
                'name' => $address->getName() ?? '',
            ])
            ->values()
            ->all();
    }
}
