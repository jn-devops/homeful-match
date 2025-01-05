<?php

return [
    'callback' => env('CALLBACK'),
    'cache' => [
        'ttl' => env('CACHE_TTL', 60)
    ],
    'matches' => [
        'limit' => env('MATCHES_LIMIT', 3),
        'verbose' => env('MATCHES_VERBOSE', 0),
    ],
];
