<?php

return [
    'sentry' => [
        'raven' => []
    ],
    'sentry_factories' => [
        'raven' => 'Facile\SentryModule\Service\RavenClientFactory',
        'ravenoptions' => 'Facile\SentryModule\Service\RavenOptionsFactory'
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Facile\SentryModule' => 'Facile\SentryModule\ServiceFactory\AbstractSentryServiceFactory',
        ]
    ],
];
