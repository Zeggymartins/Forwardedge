<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class Mailchimp
{
    public static function client(): ApiClient
    {
        $mc = new ApiClient();
        $mc->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => config('services.mailchimp.server_prefix'),
        ]);
        return $mc;
    }

    public static function listId(): string
    {
        return (string) config('services.mailchimp.list_id');
    }
}
