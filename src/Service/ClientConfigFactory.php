<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;

final class ClientConfigFactory
{
    /**
     * @psalm-suppress MissingClosureParamType
     */
    public function __invoke(ContainerInterface $container): ClientInterface
    {
        $config = $container->get('config')['sentry'] ?? [];

        $nullFilter = static function ($value): bool {
            return null !== $value;
        };

        /** @var array<string, mixed> $options */
        $options = \array_filter(
            $config['options'] ?? [],
            $nullFilter
        );

        if (\is_string($options['before_breadcrumb'] ?? null)) {
            $options['before_breadcrumb'] = $container->get($options['before_breadcrumb']);
        }

        if (\is_string($options['before_send'] ?? null)) {
            $options['before_send'] = $container->get($options['before_send']);
        }

        if (\is_string($options['traces_sampler'] ?? null)) {
            $options['traces_sampler'] = $container->get($options['traces_sampler']);
        }

        $builder = ClientBuilder::create($options);

        if (\is_string($config['representation_serializer'] ?? null)) {
            $builder->setRepresentationSerializer($container->get($config['representation_serializer']));
        }

        if (\is_string($config['serializer'] ?? null)) {
            $builder->setSerializer($container->get($config['serializer']));
        }

        return $builder->getClient();
    }
}
