<?php

namespace Facile\SentryModule;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'facile' => [
        'sentry' => [
            'client' => [],
            'configuration' => [
                'raven_javascript_dsn' => '',
                'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.0.4/raven.min.js',
                'inject_raven_javascript' => false,
            ]
        ],
        'sentry_factories' => [
            'client' => Service\ClientFactory::class,
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            ServiceFactory\AbstractServiceFactory::class,
        ],
        'factories' => [
            Service\ErrorHandlerRegister::class => InvokableFactory::class,
            Listener\ErrorHandlerListener::class => InvokableFactory::class,
            Options\ConfigurationOptions::class => Service\ConfigurationOptionsFactory::class,
        ],
        'shared' => [
            Service\ErrorHandlerRegister::class => false,
        ]
    ],
];
