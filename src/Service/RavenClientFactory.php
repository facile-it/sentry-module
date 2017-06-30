<?php
declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ConfigurationInterface;
use Interop\Container\ContainerInterface;

class RavenClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $container->get(ConfigurationInterface::class);
        $options = $configuration->getRavenOptions();

        return new \Raven_Client($configuration->getDsn(), $options);
    }

}
