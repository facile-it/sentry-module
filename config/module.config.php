<?php

return [
    'sentry' => [
        'raven' => [
            'default' => [
                'dsn' => '',
                 'options' => ''
            ]
        ]
    ],
    'sentry_factories' => [
        'raven' => [
            'factoryClass' => 'Facile\SentryModule\Service\RavenClientFactory',
            'optionsClass' => 'Facile\SentryModule\Options\RavenClient'
        ]
    ]
];