<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;

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
         * @psalm-var Options $options
         */
        $options = array_filter(
            $config['options'] ?? [],
            /**
             * @param mixed $value
             */
            static fn ($value): bool => null !== $value
        );

        $beforeBreadcrumb = $options['before_breadcrumb'] ?? null;

        if (\is_string($beforeBreadcrumb)) {
            /** @psalm-suppress MixedAssignment */
            $options['before_breadcrumb'] = $container->get($beforeBreadcrumb);
        }

        $beforeSend = $options['before_send'] ?? null;

        if (\is_string($beforeSend)) {
            /** @psalm-suppress MixedAssignment */
            $options['before_send'] = $container->get($beforeSend);
        }

        $tracesSampler = $options['traces_sampler'] ?? null;
        if (\is_string($tracesSampler)) {
            /** @psalm-suppress MixedAssignment */
            $options['traces_sampler'] = $container->get($tracesSampler);
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

        $serializer = $options['serializer'] ?? null;
        if (\is_string($serializer)) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress MixedArgument
             */
            $builder->setSerializer($container->get($serializer));
        }

        return $builder->getClient();
    }
}
