<?php

return [
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => parse_url(env('REDIS_URL'), PHP_URL_HOST),
        'password' => parse_url(env('REDIS_URL'), PHP_URL_PASS),
        'port' => parse_url(env('REDIS_URL'), PHP_URL_PORT),
        'database' => env('REDIS_CACHE_DB', '1'),
        'scheme' => 'tls',
    ],
];