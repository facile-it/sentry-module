<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ConfigurationOptions;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConfigurationOptionsFactory.
 */
final class ConfigurationOptionsFactory implements FactoryInterface
{
    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConfigurationOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConfigurationOptions
    {
        return $this($serviceLocator, ConfigurationOptions::class);
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return ConfigurationOptions
     *
     * @throws ServiceNotFoundException   if unable to resolve the service
     * @throws ServiceNotCreatedException if an exception is raised when
     *                                    creating a service
     * @throws ContainerException         if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConfigurationOptions
    {
        return new ConfigurationOptions($container->get('config')['facile']['sentry']['configuration']);
    }
}
