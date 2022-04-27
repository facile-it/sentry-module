<?php

declare(strict_types=1);

namespace Facile\SentryModule;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Sentry\ClientInterface;
use Sentry\State\HubInterface;

final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'sentry' => [
                'disable_module' => false,
                'options' => [
                    'dsn' => '',
                ],
                'javascript' => [
                    'inject_script' => false,
                    'script' => [
                        'src' => 'https://browser.sentry-cdn.com/6.19.7/bundle.min.js',
                        'integrity' => 'sha384-KXjn4K+AYjp1cparCXazrB+5HKdi69IUYz8glD3ySH3fnDgMX3Wg6VTMvXUGr4KB',
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

    /**
     * @return array<string, mixed>
     */
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
