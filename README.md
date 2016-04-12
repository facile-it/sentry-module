# ZF2 Sentry Module

[![Build Status](https://api.travis-ci.org/facile-it/sentry-module.svg?branch=master)](https://travis-ci.org/facile-it/sentry-module)
[![Code Coverage](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/facile-it/sentry-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/facile-it/sentry-module/?branch=master)

This module allows to integrate the Raven Sentry Client into Zend Framework 2.

## Installation
The only supported way to install this module is trough composer. For composer documentation you can refer to [getcomposer.org](http://getcomposer.org).

```
php composer.phar require facile-it/sentry-module
```

## Configuration

### Raven Client

To configure an instance of the raven client you can do as below:
If you need to have multiple instances just add a new one replacing `default` with the chosed name.
In order to pass options to the `\Raven_Client` you just need to add them under the `options` node.
A list of possible raven options can be found [here](https://github.com/getsentry/raven-php/blob/bd247ca2a8fd9ccfb99b60285c9b31286384a92b/lib/Raven/Client.php#L52-L76)

```php
//...
'sentry' => [
    'raven' => [
        'default' => [
            'dsn' => 'http://public:secret@example.com/1',
            'options' => []
        ]
    ]
]
//...
```

Now you can use the Raven Client by retrieving it from the Service Locator.


```php
/* @var $ravenClient \Raven_Client */
$ravenClient = $this->getServiceLocator()->get('sentry.raven.default');
```
