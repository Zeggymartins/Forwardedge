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
    | These patterns are checked in the MessageController detectSpam method.
    | The default patterns block "RobertHen" variations.
    |
    */
    'name_patterns' => [
        'roberthen',
        'robert hen',
        'robert-hen',
        'robert_hen',
    ],
];
