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
            'queues' => [
                'informer_delay_sending_push_messages' => [
                    'bind' => [
                        'exchange' => 'message_informer_delay',
                        'routing_key' => 'informer_delay.message.sending_push_messages',
                    ],
                    'declare' => [
                        'passive' => false,
                        'durable' => true,
                        'exclusive' => false,
                        'auto_delete' => false,
                        'nowait' => false,
                        'arguments' => [],
                        'ticket' => null,
                    ],
                ],
            ],
            'exchanges' => [
                'message_informer_delay' => [
                    'type' => 'x-delayed-message',
                    'passive' => false,
                    'durable' => true,
                    'exclusive' => false,
                    'auto_delete' => false,
                    'nowait' => false,
                    'arguments' => [],
                    'ticket' => null,
                ],
            ],
        ],
    ],
];