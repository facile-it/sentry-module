<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class RavenOptionsFactory extends AbstractFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $options = isset($config['sentry']['raven'][$this->name]) ? $config['sentry']['raven'][$this->name] : null;

        if (!$options || !is_array($options)) {
            throw new \RuntimeException(
                sprintf(
                    '%s: No options found for sentry.raven.%s',
                    __METHOD__,
                    $this->name
                )
            );
        }
        return new RavenClientOptions($options);
    }
}
