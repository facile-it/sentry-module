<?php

declare(strict_types=1);

namespace Facile\SentryModule\Log\Writer;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Facile\SentryModule\Exception;

final class SentryFactory implements FactoryInterface
{
    /**
     * zend-servicemanager v2 support for invocation options.
     *
     * @param array
     */
    private $creationOptions;

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return Sentry
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     *
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws ServiceNotFoundException                       if unable to resolve the service
     * @throws ServiceNotCreatedException                     if an exception is raised when
     *                                                        creating a service
     * @throws ContainerException                             if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Sentry
    {
        $options = $options ?: [];

        if (! array_key_exists('client', $options)) {
            throw new Exception\InvalidArgumentException('No client specified');
        }

        $options['client'] = $container->get($options['client']);

        return new Sentry($options);
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Sentry
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Sentry
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this($serviceLocator, Sentry::class, $this->creationOptions ?: []);
    }

    /**
     * zend-servicemanager v2 support for invocation options.
     *
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
