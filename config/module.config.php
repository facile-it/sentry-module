<?php

namespace Facile\SentryModule;

use Raven_Client;
use Zend\ServiceManager\Factory\InvokableFactory;
use Facile\Sentry\Common;

return [
    'facile' => [
        'sentry' => [
            'raven_javascript_dsn' => '',
            'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.16.0/raven.min.js',
            'raven_javascript_options' => [],
            'inject_raven_javascript' => false,
            'error_handler_options' => [
                'error_types' => null,
                'skip_exceptions' => [],
            ],
            'stack_trace_options' => [
                'ignore_backtrace_namespaces' => [
                    __NAMESPACE__,
                    'Facile\\Sentry\\Common',
                    'Facile\\Sentry\\Log',
                    'Zend\\Log',
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            Common\Sanitizer\SanitizerInterface::class => Common\Sanitizer\Sanitizer::class,
        ],
        'factories' => [
            Raven_Client::class => Service\RavenClientFactory::class,
            Listener\ErrorHandlerListener::class => Listener\ErrorHandlerListenerFactory::class,
            Options\Configuration::class => Service\ConfigurationFactory::class,
            Options\ConfigurationInterface::class => Service\ConfigurationFactory::class,
            Common\Sanitizer\Sanitizer::class => InvokableFactory::class,
            Common\StackTrace\StackTraceInterface::class => Service\StackTraceFactory::class,
            Common\Sender\SenderInterface::class => Service\SenderFactory::class,
        ],
    ],
    'log_writers' => [
        'factories' => [
            Log\Writer\Sentry::class => Log\Writer\SentryFactory::class,
        ],
    ],
];
