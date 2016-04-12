<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2016 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class RavenClientFactory extends AbstractFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionsArray = $serviceLocator->get('Config')['sentry']['raven'][$this->name];
        /* @var $options RavenClientOptions */
        $options = new RavenClientOptions($optionsArray);

        return new \Raven_Client($options->getDsn(), $options->getOptions());
    }
}
