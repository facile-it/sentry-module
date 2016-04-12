<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2016 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModule\ServiceFactory;

use Facile\SentryModule\Service\AbstractFactory;
use RuntimeException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractSentryServiceFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritDoc}
     * @throws ServiceNotFoundException
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return false !== $this->getServiceFactoryMapping($serviceLocator, $requestedName);
    }

    /**
     * {@inheritDoc}
     * @throws ServiceNotFoundException
     * @throws RuntimeException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $mapping = $this->getServiceFactoryMapping($serviceLocator, $name);

        if (!$mapping) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mapping['factoryClass'];
        $name = $mapping['name'];

        /* @var $factory AbstractFactory */
        $factory = new $factoryClass($name);

        return $factory->createService($serviceLocator);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @return array|bool
     * @throws ServiceNotFoundException
     */
    protected function getServiceFactoryMapping(ServiceLocatorInterface $serviceLocator, $name)
    {
        $matches = [];

        if (!preg_match('/^sentry\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $serviceLocator->get('Config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (!isset($config['sentry_factories'][$serviceType])) {
            return false;
        }

        if (!isset($config['sentry'][$serviceType][$serviceName])) {
            return false;
        }

        return [
            'name' => $serviceName,
            'factoryClass' => $config['sentry_factories'][$serviceType],
        ];
    }
}
