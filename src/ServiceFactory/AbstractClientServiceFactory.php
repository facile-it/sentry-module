<?php

namespace Facile\SentryModule\ServiceFactory;

use Facile\SentryModule\Service\AbstractFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractClientServiceFactory.
 */
class AbstractClientServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return false !== $this->getServiceFactoryMapping($container, $requestedName);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AbstractFactory
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mapping = $this->getServiceFactoryMapping($container, $requestedName);

        if (!$mapping) {
            throw new ServiceNotFoundException();
        }

        /* @var array $mapping */
        $factoryClass = $mapping['factoryClass'];
        $name = $mapping['name'];

        $factory = new $factoryClass($name);

        return $factory($container, $requestedName);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $name
     *
     * @return array|bool
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    protected function getServiceFactoryMapping(ContainerInterface $container, $name)
    {
        $matches = [];

        $pattern = '/^facile\.sentry\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/';
        if (!preg_match($pattern, $name, $matches)) {
            return false;
        }

        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (!isset($config['facile']['sentry_factories'][$serviceType])) {
            return false;
        }

        if (!isset($config['facile']['sentry'][$serviceType][$serviceName])) {
            return false;
        }

        return [
            'name' => $serviceName,
            'factoryClass' => $config['facile']['sentry_factories'][$serviceType],
        ];
    }
}
