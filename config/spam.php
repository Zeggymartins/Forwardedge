<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blacklisted Email Addresses
    |--------------------------------------------------------------------------
    |
    | Email addresses that should be blocked from submitting contact forms.
    | Add emails used by spammers here.
    |
    */
    'blacklisted_emails' => [
        // Add spammer emails here as you discover them
        // Example: 'spammer@example.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklisted IP Addresses
    |--------------------------------------------------------------------------
    |
    | IP addresses that should be blocked from submitting contact forms.
    | Add IPs of persistent spammers here.
    |
    */
    'blacklisted_ips' => [
        // Add spammer IPs here as you discover them
        // Example: '123.456.789.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Spam Name Patterns
    |--------------------------------------------------------------------------
    |
    | Names containing these patterns will be blocked (case-insensitive).
    | Spaces are ignored during matching.
    |
    */
    'name_patterns' => [
        'roberthen',
        'robert hen',
        'robert-hen',
        'robert_hen',
        // Add more spam names here as you discover them
    ],

    /*
    |--------------------------------------------------------------------------
    | Spam Message Keywords
    |--------------------------------------------------------------------------
    |
    | Messages containing 2 or more of these keywords will be blocked.
    | This catches common spam phrases while allowing legitimate messages.
    |
    */
    'message_keywords' => [
        'click here',
        'buy now',
        'limited time',
        'act now',
        'viagra',
        'cialis',
        'weight loss',
        'make money',
        'work from home',
        'earn cash',
        'casino',
        'lottery',
        'winner',
        'congratulations you won',
        'bitcoin',
        'crypto investment',
        'double your money',
        'guaranteed income',
        'free money',
        'prize',
        'claim your',
        'act immediately',
        'expire soon',
        'urgent response',
        // Add more spam keywords here
    ],

    /*
    |--------------------------------------------------------------------------
    | Disposable Email Domains
    |--------------------------------------------------------------------------
    |
    | Temporary/disposable email services often used by spammers.
    | Messages from these domains will be blocked.
    |
    */
    'disposable_domains' => [
        'tempmail.com',
        'guerrillamail.com',
        '10minutemail.com',
        'throwaway.email',
        'mailinator.com',
        'trashmail.com',
        'temp-mail.org',
        'getnada.com',
        'maildrop.cc',
        'sharklasers.com',
        'guerrillamail.info',
        'grr.la',
        'guerrillamail.biz',
        'guerrillamail.de',
        'spam4.me',
        'mail-temp.com',
        '10mail.org',
        'tempinbox.com',
        'minuteinbox.com',
        'emailondeck.com',
        // Add more disposable domains as you discover them
    ],
];
