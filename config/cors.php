<?php


return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],  // Autoriser toutes les méthodes (GET, POST, PUT, etc.)

    //'allowed_origins' => ['http://172.18.144.1:3000', 'http://172.30.192.1:3000'],

    'allowed_origins' => ['*'],
    //'allowed_origins' => ['http://172.*.*.*:3000', 'http://localhost:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],  // Autoriser tous les headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,  // Pour autoriser les cookies et informations d'identification
];
