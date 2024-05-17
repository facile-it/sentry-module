<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\SentrySdk;

/**
 * @psalm-type Options = array{before_breadcrumb?: string|callable(): mixed, before_send?: string|callable(): mixed, traces_sampler?: string|callable(): float, representation_serializer?: string, serializer?: string}
 */
final class ClientConfigFactory
{
    public function __invoke(ContainerInterface $container): ClientInterface
    {
        /** @psalm-var array{options?: Options} $config */
        $config = $container->get('config')['sentry'] ?? [];

        /**
         * @var array<string, mixed>
         *
         * @psalm-var Options $options
         */
        $options = array_filter(
            $config['options'] ?? [],
            /**
             * @param mixed $value
             */
            static fn($value): bool => null !== $value
        );

        $serviceKeys = [
            'logger',
            'before_send',
            'before_send_transaction',
            'before_send_check_in',
            'before_breadcrumb',
            'transport',
            'traces_sampler',
        ];

        foreach ($serviceKeys as $serviceKey) {
            if ($options && isset($options[$serviceKey]) && is_string($options[$serviceKey])) {
                /** @psalm-suppress MixedAssignment */
                $options[$serviceKey] = $container->get($options[$serviceKey]);
            }
        }

        $builder = ClientBuilder::create($options);

        $representationSerializer = $options['representation_serializer'] ?? null;
        if (\is_string($representationSerializer)) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress MixedArgument
             */
            $builder->setRepresentationSerializer($container->get($representationSerializer));
        }

        $client = $builder->getClient();

        $currentClient = SentrySdk::getCurrentHub()->getClient();
        if (! $currentClient) {
            SentrySdk::getCurrentHub()->bindClient($client);
        }

        return $client;
    }
}
