<?php
declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Facile\SentryModule\Options\ConfigurationInterface;
use Interop\Container\ContainerInterface;
use Raven_Client;

class ErrorHandlerListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var \Raven_Client $client */
        $client = $container->get(Raven_Client::class);
        /** @var ConfigurationInterface $configuration */
        $configuration = $container->get(ConfigurationInterface::class);

        $options = $configuration->getErrorHandlerOptions();

        return new ErrorHandlerListener($client, $options);
    }
}
