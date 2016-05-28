<?php

namespace Facile\SentryModule;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'facile' => [
        'sentry' => [
            'client' => [],
        ],
        'sentry_factories' => [
            'client' => Service\ClientFactory::class,
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            ServiceFactory\AbstractClientServiceFactory::class,
        ],
        'factories' => [
            Service\ErrorHandlerRegister::class => InvokableFactory::class,
            Listener\ErrorHandlerListener::class => InvokableFactory::class,
        ],
        'shared' => [
            Service\ErrorHandlerRegister::class => false,
        ]
    ],
];
