<?php
declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\Sentry\Common\StackTrace\StackTrace;
use Facile\Sentry\Common\StackTrace\StackTraceInterface;
use Facile\SentryModule\Options\ConfigurationInterface;
use Interop\Container\ContainerInterface;
use Raven_Client;

class StackTraceFactory
{
    public function __invoke(ContainerInterface $container): StackTraceInterface
    {
        /** @var Raven_Client $client */
        $client = $container->get(Raven_Client::class);
        /** @var ConfigurationInterface $configuration */
        $configuration = $container->get(ConfigurationInterface::class);

        $serializer = $container->has(\Raven_Serializer::class) ? $container->get(\Raven_Serializer::class) : null;
        $reprSerializer = $container->has(\Raven_ReprSerializer::class) ? $container->get(\Raven_ReprSerializer::class) : null;

        $stackTrace = new StackTrace(
            $client,
            $serializer,
            $reprSerializer,
            $configuration->getStackTraceOptions()->getIgnoreBacktraceNamespaces()
        );

        return $stackTrace;
    }
}
