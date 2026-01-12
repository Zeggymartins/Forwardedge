<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Secret Admin Setup Key
    |--------------------------------------------------------------------------
    |
    | This key protects the secret admin setup page. Only someone with this
    | key can create or update the admin account. Keep this secret!
    |
    | Generate a random key: openssl rand -hex 32
    |
    */
    'secret_setup_key' => env('ADMIN_SECRET_SETUP_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Secret Admin Setup URL Path
    |--------------------------------------------------------------------------
    |
    | The URL path for the secret admin setup page. Change this to something
    | unique and hard to guess. Do NOT use common paths like /admin-setup.
    |
    | Example: 'xk9p2m5n8q4r7t3w' results in URL: /xk9p2m5n8q4r7t3w
    |
    */
    'secret_setup_path' => env('ADMIN_SECRET_SETUP_PATH', 'secret-admin-setup-x9k2p5m8'),
];
