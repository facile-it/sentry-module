<?php

declare(strict_types=1);

namespace Facile\SentryModule;

use Sentry\ClientInterface;
use Sentry\State\HubInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'sentry' => [
                'options' => [
                    'dsn' => '',
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
                    ],
                ],
            ],
            'log_writers' => [
                'factories' => [
                    Log\Writer\Sentry::class => InvokableFactory::class,
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                ClientInterface::class => Service\ClientConfigFactory::class,
                HubInterface::class => Service\HubFactory::class,
                Listener\ErrorHandlerListener::class => Listener\ErrorHandlerListenerFactory::class,
            ],
        ];
    }
}
