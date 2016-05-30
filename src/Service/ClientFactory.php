<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Processor\SanitizeDataProcessor;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientFactory.
 */
class ClientFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return Client
     * @throws \RuntimeException
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ClientOptions $options */
        $options = $this->getOptions($container, 'client');

        $ravenOptions = $options->getOptions();

        if (!array_key_exists('processors', $ravenOptions)) {
            $ravenOptions['processors'] = [SanitizeDataProcessor::class];
        }

        $ravenClient = new \Raven_Client($options->getDsn(), $ravenOptions);

        $client = new Client($ravenClient, $options);

        $errorHandlerListener = $container->get($options->getErrorHandlerListener());
        if ($errorHandlerListener instanceof ClientAwareInterface) {
            $errorHandlerListener->setClient($client);
        }
        $client->setErrorHandlerListener($errorHandlerListener);

        return $client;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Client::class);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return ClientOptions::class;
    }
}
