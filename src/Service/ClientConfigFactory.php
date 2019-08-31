<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;

final class ClientConfigFactory
{
    public function __invoke(ContainerInterface $container): ClientInterface
    {
        $config = $container->get('config')['sentry'];

        $options = \array_filter(
            $config['options'] ?? [],
            static function ($value) {
                return null !== $value;
            }
        );

        $resolves = [
            'before_breadcrumbs',
            'before_send',
        ];

        $options = \array_merge(
            $options,
            \array_map(
                static function (string $value) use ($container): callable {
                    return $container->get($value);
                },
                \array_intersect_key($options, \array_flip($resolves))
            )
        );

        $builder = ClientBuilder::create($options);

        if (\is_string($config['transport'] ?? null)) {
            $builder->setTransport($container->get($config['transport']));
        }

        if (\is_string($config['http_client'] ?? null)) {
            $builder->setHttpClient($container->get($config['http_client']));
        }

        if (\is_string($config['message_factory'] ?? null)) {
            $builder->setMessageFactory($container->get($config['message_factory']));
        }

        if (\is_string($config['uri_factory'] ?? null)) {
            $builder->setUriFactory($container->get($config['uri_factory']));
        }

        if (\is_string($config['representation_serializer'] ?? null)) {
            $builder->setRepresentationSerializer($container->get($config['representation_serializer']));
        }

        if (\is_string($config['serializer'] ?? null)) {
            $builder->setSerializer($container->get($config['serializer']));
        }

        return $builder->getClient();
    }
}
