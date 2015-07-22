<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2015 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

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
