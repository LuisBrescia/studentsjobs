<?php
require __DIR__ . '/vendor/autoload.php';

$client = new Predis\Client(getenv('REDIS_URL'), [
    'parameters' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],
]);

$client->set('teste', 'funciona?');
echo $client->get('teste');
