<?php

return [
    'sentry' => [
        'raven' => []
    ],
    'sentry_factories' => [
        'raven' => [
            'factoryClass' => 'Facile\SentryModule\Service\RavenClientFactory',
            'optionsClass' => 'Facile\SentryModule\Options\RavenClient'
        ]
    ]
];