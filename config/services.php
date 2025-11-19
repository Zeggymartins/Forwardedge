<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paystack' => [
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'secret' => env('PAYSTACK_SECRET_KEY'),
        'webhook_url' => env('PAYSTACK_WEBHOOK_URL', '/payment/webhook'),
    ],

    'mailchimp' => [
        'key'                => env('MAILCHIMP_KEY'),
        'server_prefix'      => env('MAILCHIMP_SERVER_PREFIX'),
        'list_id'            => env('MAILCHIMP_LIST_ID'),
        'transactional_key'  => env('MAILCHIMP_TRANSACTIONAL_KEY'),
        'double_opt_in'      => env('MAILCHIMP_DOUBLE_OPT_IN', false),
    ],

    'recaptcha' => [
        'key'    => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
        'score'  => env('RECAPTCHA_MIN_SCORE', 0.5),
    ],

    'meta_pixel' => [
        'id' => env('META_PIXEL_ID'),
    ],
];
