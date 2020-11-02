# ZF Sentry Module

[![Build Status](https://api.travis-ci.org/facile-it/sentry-module.svg?branch=master)](https://travis-ci.org/facile-it/sentry-module)
[![Code Coverage](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)

This module allows integration with Sentry Client into laminas and mezzio.

## Installation

The only supported way to install this module is trough composer. For composer documentation you can refer to [getcomposer.org](http://getcomposer.org).

```
php composer.phar require facile-it/sentry-module
```


## Configuration

### Client

To configure an instance of the client you can do as below:

```php

return [
    'sentry' => [
        'disable_module' => false,
        'options' => [
            'dsn' => '',
            // other sentry options
            // https://docs.sentry.io/error-reporting/configuration/?platform=php
        ],
        'javascript' => [
            'inject_script' => false,
            'script' => [
                'src' => 'https://browser.sentry-cdn.com/5.6.3/bundle.min.js',
                'integrity' => 'sha384-/Cqa/8kaWn7emdqIBLk3AkFMAHBk0LObErtMhO+hr52CntkaurEnihPmqYj3uJho',
                'crossorigin' => 'anonymous',
            ],
            'options' => [
                'dsn' => '',
                // other sentry options
                // https://docs.sentry.io/error-reporting/configuration/?platform=php
            ],
        ],
    ],
];
//...
```

Now you can use the client and the Hub by retrieving it from the Service Locator.

```php
use Sentry\HubInterface;

$hub = $this->getServiceLocator()->get(HubInterface::class);
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
use Laminas\Mvc\MvcEvent;

class Module 
{
    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        $container = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        
        /** @var ErrorHandlerListener $errorHandlerListener */
        $errorHandlerListener = $container->get(ErrorHandlerListener::class);
        $errorHandlerListener->attach($eventManager);
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
                                    'priority' => \Laminas\Log\Logger::ERR,
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

### Javascript

This module can inject the javascript Raven client library and configure it for you.

```php
<?php

// facile-sentry.module.local.php
return [
    'sentry' => [
        'javascript' => [
            'inject_script' => true, // enable it
            'options' => [
                'dsn' => '',
                // other sentry options
                // https://docs.sentry.io/error-reporting/configuration/?platform=php
            ],
            // script options (defaults)
            'script' => [
                'src' => 'https://browser.sentry-cdn.com/5.6.3/bundle.min.js',
                'integrity' => 'sha384-/Cqa/8kaWn7emdqIBLk3AkFMAHBk0LObErtMhO+hr52CntkaurEnihPmqYj3uJho',
                'crossorigin' => 'anonymous',
            ],
        ],
    ],
];

```
In your layout:
```php
<?= $this->headScript() ?>
```

## Usage with Mezzio (ex zend-expressive)

If you want to use it with Mezzio you should initialize the Sentry client and Hub.
You can simply retrieve the HubInterface service to initialize it.

```php
use Sentry\HubInterface;

$container->get(HubInterface::class);
```
