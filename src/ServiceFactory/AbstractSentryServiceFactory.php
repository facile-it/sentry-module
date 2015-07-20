<?php

namespace Facile\SentryModule\ServiceFactory;

use Facile\SentryModule\Options\OptionsParser;
use Facile\SentryModule\Service\AbstractFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractSentryServiceFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return false !== $this->getServiceFactoryMapping($serviceLocator, $requestedName);
    }

    /**
     * {@inheritDoc}
     * @throws ServiceNotFoundException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $mapping = $this->getServiceFactoryMapping($serviceLocator, $name);

        if (!$mapping) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mapping['factoryClass'];
        $optionsClass = $mapping['optionsClass'];
        $type = $mapping['type'];
        $name = $mapping['name'];

        $optionsParser = new OptionsParser($serviceLocator->get('Config'), $type, $name, $optionsClass);

        /* @var $factory AbstractFactory */
        $factory = new $factoryClass($optionsParser->getOptions());

        $factory->createService($serviceLocator);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @return array|bool
     */
    protected function getServiceFactoryMapping(ServiceLocatorInterface $serviceLocator, $name)
    {
        $matches = [];

        if (!preg_match('/^sentry\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $serviceLocator->get('Config');
        $serviceType = $matches['type'];
        $serviceName = $matches['name'];

        if (!isset($config['sentry_factories'][$serviceType])) {
            return false;
        }

        if (!isset($config['sentry'][$serviceType][$serviceName])) {
            return false;
        }

        return [
            'type' => $serviceType,
            'name' => $serviceName,
            'factoryClass' => $config['sentry_factories'][$serviceType]['factoryClass'],
            'optionsClass' => $config['sentry_factories'][$serviceType]['optionsClass']
        ];
    }
}