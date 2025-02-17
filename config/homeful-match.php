<?php

use Homeful\Common\Classes\Input;

return [
    'callback' => env('CALLBACK'),
    'cache' => [
        'ttl' => env('CACHE_TTL', 60)
    ],
    'matches' => [
        'limit' => env('MATCHES_LIMIT', 3),
        'verbose' => env('MATCHES_VERBOSE', 0),
    ],
    'override_maximum_paying_age' => env('OVERRIDE_MAXIMUM_PAYING_AGE', 65),
    'params' => [
        Input::INCOME_REQUIREMENT_MULTIPLIER => 0.30
    ],
];
