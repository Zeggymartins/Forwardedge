<?php

return [
    'twilio' => [
        'enabled' => env('TWILIO_LOOKUP_ENABLED', false),
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'fields' => env('TWILIO_LOOKUP_FIELDS', 'carrier,line_type_intelligence'),
    ],
];
