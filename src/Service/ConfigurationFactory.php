<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Options\Configuration;
use Facile\SentryModule\Options\ConfigurationInterface;
use Facile\SentryModule\Options\ErrorHandlerOptions;
use Facile\SentryModule\Options\StackTraceOptions;
use Facile\SentryModule\SendCallback\CallbackChain;
use Interop\Container\ContainerInterface;

/**
 * Class ConfigurationFactory.
 */
final class ConfigurationFactory
{
    /**
     * @param ContainerInterface $container
     * @return ConfigurationInterface
     */
    public function __invoke(ContainerInterface $container): ConfigurationInterface
    {
        /** @var array $config */
        $config = $container->get('config');

        $configuration = $config['facile']['sentry'] ?? [];
        $ravenOptions = $configuration['raven_options'] ?? [];

        if (! array_key_exists('logger', $ravenOptions)) {
            $ravenOptions['logger'] = 'SentryModule';
        }

        if (array_key_exists('send_callback', $ravenOptions)) {
            $ravenOptions['send_callback'] = $this->buildCallbackChain($container, $ravenOptions['send_callback']);
        }

        if (array_key_exists('transport', $ravenOptions)) {
            $transport = $ravenOptions['transport'];
            if (is_string($transport)) {
                $transport = $container->get($transport);
            }
            $ravenOptions['transport'] = $transport;
        }

        $configuration['raven_options'] = $ravenOptions;

        $configuration['error_handler_options'] = new ErrorHandlerOptions(
            $configuration['error_handler_options'] ?? []
        );

        $configuration['stack_trace_options'] = new StackTraceOptions(
            $configuration['stack_trace_options'] ?? []
        );

        return new Configuration($configuration);
    }


    /**
     * @param ContainerInterface $container
     * @param array|string|callable $callbackOptions
     *
     * @return CallbackChain
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     */
    private function buildCallbackChain(ContainerInterface $container, $callbackOptions): CallbackChain
    {
        if (null === $callbackOptions) {
            return new CallbackChain();
        }

        $callbackOptions = (array) $callbackOptions;
        $callbacks = [];

        foreach ($callbackOptions as $callbackItem) {
            if (is_string($callbackItem) && $container->has($callbackItem)) {
                $callbackItem = $container->get($callbackItem);
            }
            if (! $this->isValidCallback($container, $callbackItem)) {
                throw new InvalidArgumentException('Invalid callback');
            }

            $callbacks[] = $callbackItem;
        }

        return new CallbackChain($callbacks);
    }

    /**
     * @param ContainerInterface $container
     * @param callable|string $callbackItem
     * @return bool
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     */
    private function isValidCallback(ContainerInterface $container, $callbackItem): bool
    {
        return is_callable($callbackItem) || (is_string($callbackItem) && $container->has($callbackItem));
    }
}
