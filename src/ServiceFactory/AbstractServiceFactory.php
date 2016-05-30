<?php

namespace Facile\SentryModule\ServiceFactory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractServiceFactory.
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var string
     */
    protected $configKey = 'facile';

    /**
     * @param ServiceLocatorInterface $services
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @return bool
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return $this->canCreate($services, $requestedName);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     *
     * @return bool
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return false !== $this->getFactoryMapping($container, $requestedName);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $name
     *
     * @return array
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getFactoryMapping(ContainerInterface $container, $name)
    {
        $matches = [];

        $pattern = '/^%s\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/';
        $pattern = sprintf($pattern, $this->configKey.'.sentry');
        if (!preg_match($pattern, $name, $matches)) {
            return false;
        }
        /** @var array $config */
        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        $moduleConfig = $config[$this->configKey]['sentry'];
        $factoryConfig = $config[$this->configKey]['sentry_factories'];

        if (!isset($factoryConfig[$serviceType], $moduleConfig[$serviceType][$serviceName])) {
            return false;
        }

        return [
            'serviceType' => $serviceType,
            'serviceName' => $serviceName,
            'factoryClass' => $factoryConfig[$serviceType],
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return mixed
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws ServiceNotFoundException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var $factory \Facile\SentryModule\Service\AbstractFactory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, $requestedName);
    }

    /**
     * @param ServiceLocatorInterface $services
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return $this($services, $requestedName);
    }
}
