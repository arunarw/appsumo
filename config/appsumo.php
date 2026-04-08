<?php

return [
    'tiers' => [
        1 => ['name' => 'Tier 1', 'seats' => 5, 'plan' => 'Starter'],
        2 => ['name' => 'Tier 2', 'seats' => 10, 'plan' => 'Starter'],
        3 => ['name' => 'Tier 3', 'seats' => 15, 'plan' => 'Essential'],
        4 => ['name' => 'Tier 4', 'seats' => 20, 'plan' => 'Essential'],
        5 => ['name' => 'Tier 5', 'seats' => 25, 'plan' => 'Essential'],
    ],

    'webhook_url' => env('CLIENT_WEBHOOK_URL'),
    'api_key' => env('CLIENT_API_KEY'),
    'oauth_redirect_url' => env('CLIENT_OAUTH_REDIRECT_URL'),
    'client_id' => env('APPSUMO_CLIENT_ID'),
    'client_secret' => env('APPSUMO_CLIENT_SECRET'),
];
