<?php

return [
    'isDebugging' => true,
    'connectionDefault' => 'default',
    'connections' => [
        'default' => [
            'host' => 'localhost',
            'port' => '5672',
            'username' => 'admin',
            'password' => 'Qwertyu1',
            'vhost' => '/',
        ],
    ],
    'producerDefault' => 'default',
    'producers' => [
        'default' => [
            'enabled' => true,
        ]
    ],
];