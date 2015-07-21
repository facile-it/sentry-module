<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class RavenClientFactory extends AbstractFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionsServiceName = sprintf('sentry.ravenoptions.%s', $this->name);

        /* @var $options RavenClientOptions */
        $options = $serviceLocator->get($optionsServiceName);

        return new \Raven_Client($options->getDsn(), $options->getOptions());
    }
}
