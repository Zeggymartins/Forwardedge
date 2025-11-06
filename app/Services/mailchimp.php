<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;
use RuntimeException;

class Mailchimp
{
    public static function client(): ApiClient
    {
        $apiKey = config('services.mailchimp.key');
        $server = config('services.mailchimp.server_prefix');

        if (blank($apiKey) || blank($server)) {
            throw new RuntimeException('Mailchimp credentials are missing. Please set MAILCHIMP_KEY and MAILCHIMP_SERVER_PREFIX.');
        }

        $mc = new ApiClient();
        $mc->setConfig([
            'apiKey' => $apiKey,
            'server' => $server,
        ]);

        return $mc;
    }

    public static function listId(): string
    {
        $listId = (string) config('services.mailchimp.list_id', '');

        if (blank($listId)) {
            throw new RuntimeException('Mailchimp list ID is missing. Please set MAILCHIMP_LIST_ID.');
        }

        return $listId;
    }
}
