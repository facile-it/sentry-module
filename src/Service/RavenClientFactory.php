<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class RavenClientFactory extends AbstractFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var RavenClientOptions $options */
        $options = $this->getOptions();
        return new \Raven_Client($options->getDsn(), $options->getOptions());
    }
}
