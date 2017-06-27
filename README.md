# ZF Sentry Module

[![Build Status](https://api.travis-ci.org/facile-it/sentry-module.svg?branch=master)](https://travis-ci.org/facile-it/sentry-module)
[![Code Coverage](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)

This module allows integration with Raven Sentry Client into Zend Framework 2/3.

## Installation

The only supported way to install this module is trough composer. For composer documentation you can refer to [getcomposer.org](http://getcomposer.org).

```
php composer.phar require facile-it/sentry-module
```


## Configuration

### Client

To configure an instance of the client you can do as below:

```php
//...
return [
    'facile' => [
        'sentry' => [
            'dsn' => '', // Sentry Raven dsn
            'raven_options' => [ // Sentry Raven options
                'app_path' => '',
                'release' => 'release-hash',
                // ....
            ],
            'raven_javascript_dsn' => '', // javascript sentry dsn
            'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.16.0/raven.min.js',
            'raven_javascript_options' => [], // javascript sentry options
            'inject_raven_javascript' => false, // should we inject sentry JS file and script? 
            'error_handler_options' => [ // Error Handler Listener options (read below)
                'error_types' => null, // Error types to log, NULL will get value from error_reporting() function
                'skip_exceptions' => [], // Exception class names to skip when loggin exceptions
            ],
            'stack_trace_options' => [
                // We clean the backtrace when loggin messages removing last stacks from our library.
                // You can add more namespaces to ignore when using some other
                // libraries between the real log line and our library.
                // "Facile\SentryModule" is already present in module's configuration.
                'ignore_backtrace_namespaces' => [],
            ],
        ],
    ],
];
//...
```

Now you can use the client and the Raven client by retrieving it from the Service Locator.

```php
$client = $this->getServiceLocator()->get(\Raven_Client::class);
```

### Error Handler Listener

This module provides a listener for `MvcEvent::EVENT_DISPATCH_ERROR` and `MvcEvent::EVENT_RENDER_ERROR` events
in order to log the exceptions caught in these events.

If you want to use it you should register it in your application module.

#### Example:

```php
<?php

namespace App;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Raven_Client;

class Module 
{
    public function onBootstrap(EventInterface $e)
    {
        /* @var MvcEvent $e */
        $application = $e->getApplication();
        $container = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        
        /** @var ErrorHandlerListener $errorHandlerListener */
        $errorHandlerListener = $container->get(ErrorHandlerListener::class);
        $errorHandlerListener->attach($eventManager);
        
        // you can optionally register Raven_ErrorHandler 
        /** @var Raven_Client $client */
        $client = $container->get(Raven_Client::class);
        // $errorHandler = new \Raven_ErrorHandler($client);
        // $errorHandler->registerErrorHandler();
        // $errorHandler->registerExceptionHandler();
        // $errorHandler->registerShutdownFunction();
    }
}
```

### Log Writer

You can use our log writer to write logs.

#### Example:
```php
<?php

// global.php
return [
    'log' => [
        'application.log' => [
            'writers' => [
                [
                    'name' => \Facile\SentryModule\Log\Writer\Sentry::class,
                    'options' => [
                        'filters' => [
                            [
                                'name' => 'priority',
                                'options' => [
                                    'priority' => \Zend\Log\Logger::ERR,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ],
    ],
];
```

Usage:

```php
$logger->crit('Log this message');

// or with exceptions, to see the correct trace in sentry:
$e = new \RuntimeException('test-exception');
$logger->crit($e->getMessage(), ['exception' => $e]);
```

If you are interested on a PSR3 compatible log you can use [facile-it/sentry-psr-log](https://github.com/facile-it/sentry-psr-log).

### Javascript

This module can inject the javascript Raven client library and configure it for you.

```php
<?php

// facile-sentry.module.local.php
return [
    'facile' => [
        'sentry' => [
            'raven_javascript_dsn' => '', // (public dsn to use)
            'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.16.0/raven.min.js', // (default)
            'raven_javascript_options' => [
                'release' => 'release-hash',
            ],
            'inject_raven_javascript' => true, // (default false)
        ]
    ]
];

```
In your layout:
```php
<?= $this->headScript() ?>
```
