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
If you need to have multiple instances just add a new one replacing `default` with the chosen name.
In order to pass options to the `\Raven_Client` you just need to add them under the `options` key.
A list of possible raven options can be found [here](https://github.com/getsentry/sentry-php/blob/435f29c76df8c0aef102980be7fcce574de4ed0f/lib/Raven/Client.php#L57-L89)

```php
//...
'facile' => [
    'sentry' => [
        'client' => [
            'default' => [
                'dsn' => 'http://public:secret@example.com/1',
                'options' => [], // Raven client options
                'register_error_handler' => false,
                'register_exception_handler' => false,
                'register_shutdown_function' => false,
                'register_error_listener' => false,
                'error_handler_listener' => null, // custom error handler listener service
            ]
        ]
    ]
]
//...
```

Now you can use the client and the Raven client by retrieving it from the Service Locator.

```php
/* @var $client \Facile\SentryModule\Service\Client */
$client = $this->getServiceLocator()->get('facile.sentry.client.default');
$ravenClient = $client->getRaven();
```

### Error Handler Listener

This module provides a listener for `MvcEvent::EVENT_DISPATCH_ERROR` and `MvcEvent::EVENT_RENDER_ERROR` events
in order to log the exceptions caught in these events.

To enabled it set `register_error_listener` to `true`.

#### Custom Error Handler Listener

If you want to register a custom listener you can provide a service name in `error_handler_listener` to retrieve
it from the service container.  
It should implements `Zend\ServiceManager\ListenerAggregateInterface`. Be sure to set this service as not shared.

You'll probably need the client to log the event, so you can implement
`Facile\SentryModule\Service\ClientAwareInterface` and the module will automatically inject it.

Example:

```php
// facile-sentry.module.local.php
$config = [
    'facile' => [
        'sentry' => [
            'client' => [
                'default' => [
                    'dsn' => 'http://public:secret@example.com/1',
                    'options' => [],
                    'register_error_handler' => false,
                    'register_exception_handler' => false,
                    'register_shutdown_function' => false,
                    'register_error_listener' => true,
                    'error_handler_listener' => My\CustomErrorListener::class,
                ]
            ]
        ]
    ]
];

```

```php

namespace My;

use Zend\ServiceManager\ListenerAggregateInterface;
use Zend\ServiceManager\ListenerAggregateTrait;
use Facile\SentryModule\Service\ClientAwareInterface;
use Facile\SentryModule\Service\ClientAwareTrait;

class CustomErrorListener implements ListenerAggregateInterface, ClientAwareInterface
{
    use ListenerAggregateTrait;
    use ClientAwareTrait;
    
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        // ...
    }
}
```

### Log Writer

You can use our log writer to write logs.

```php
// global.php
return [
    'log' => [
        'application.log' => [
            'writers' => [
                [
                    'name' => \Facile\SentryModule\Log\Writer\Sentry::class,
                    'options' => [
                        'client' => 'facile.sentry.client.default',
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

// if you provide a different message, another exception will be created:
$e = new \RuntimeException('test-exception');
$logger->crit('An error occurred', ['exception' => $e]);
// Will log a Facile\SentryModule\Log\Writer\ContextException exception with original exception as the previous one.

```

If you are interested on a PSR3 compatible log you can use [facile-it/sentry-psr-log](https://github.com/facile-it/sentry-psr-log).

### Javascript

This module can inject the javascript Raven client library and configure it for you.

```php
// facile-sentry.module.local.php
$config = [
    'facile' => [
        'sentry' => [
            'client' => [
                'default' => [
                    // ...
                ]
            ],
            'configuration' => [
                'raven_javascript_dsn' => '', // (public dsn to use)
                'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.7.0/raven.min.js', // (default)
                'raven_javascript_options' => [
                    'release' => 'foo',
                ],
                'inject_raven_javascript' => true, // (default false)
            ]
        ]
    ]
];

```

In your layout:
```phtml
<?= $this->headScript() ?>
```


## A complete configuration example

```php
return [
    'facile' => [
        'sentry' => [
            'client' => [
                'default' => [
                    'dsn' => 'http://xxxxxxxxxxxxxxxxxx:xxxxxxxxxxxx@localhost:9000/2',
                    'options' => [
                        'auto_log_stacks' => true,
                        'curl_method' => 'async',
                        'tags' => [
                            'php_version' => phpversion(),
                        ],
                        'release' => file_exists('REVISION') ? file_get_contents('REVISION') : 'development',
                        'environment' => getenv('APP_ENV') ?: 'production',
                        'processorOptions' => [
                            Raven_SanitizeDataProcessor::class => [
                                'fields_re' => '/(authorization|password|passwd|secret|password_confirmation|card_number|auth_pw|cvv2)/i',
                                'values_re' => '/^(?:\d[ -]*?){13,16}$/',
                            ],
                            Facile\SentryModule\Processor\SanitizeDataProcessor::class => [
                                'fields_re' => '/(authorization|password|passwd|secret|password_confirmation|card_number|auth_pw|cvv2)/i',
                                'values_re' => '/^(?:\d[ -]*?){13,16}$/',
                            ],
                        ],
                        'transport' => 'my.transport.service.name',
                        'send_callback' => [
                            'my.sendcallback1.service.name',
                            'my.sendcallback2.service.name',
                        ],
                        // Other Raven options
                    ],
                    'register_error_handler' => true,
                    'register_exception_handler' => true,
                    'register_shutdown_function' => true,
                    'register_error_listener' => true,
                ],
            ],
            'configuration' => [
                'raven_javascript_dsn' => 'http://xxxxxxxxxxxxxxxx@localhost:9000/2',
                'raven_javascript_uri' => 'https://cdn.ravenjs.com/3.7.0/raven.min.js',
                'inject_raven_javascript' => true,
            ]
        ]
    ]
];

```