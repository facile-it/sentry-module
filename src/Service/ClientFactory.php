<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Processor\SanitizeDataProcessor;
use Facile\SentryModule\SendCallback\CallbackChain;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientFactory.
 */
class ClientFactory extends AbstractFactory
{

    /**
     * @param ContainerInterface $container
     * @param array|string|callable $callbackOptions
     * @return CallbackChain
     */
    protected function buildCallbackChain(ContainerInterface $container, $callbackOptions)
    {
        $callbackChain = new CallbackChain();

        if (null === $callbackOptions) {
            return $callbackChain;
        }

        if (is_callable($callbackOptions) || !is_array($callbackOptions)) {
            $callbackOptions = [$callbackOptions];
        }

        foreach ($callbackOptions as $callbackItem) {
            if (is_string($callbackItem)) {
                $callbackItem = $container->get($callbackItem);
            }

            $callbackChain->addCallback($callbackItem);
        }

        return $callbackChain;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Client
     *
     * @throws \RuntimeException
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

        if (!array_key_exists('logger', $ravenOptions)) {
            $ravenOptions['logger'] = 'SentryModule';
        }

        if (array_key_exists('send_callback', $ravenOptions)) {
            $ravenOptions['send_callback'] = $this->buildCallbackChain($container, $ravenOptions['send_callback']);
        }

        if (array_key_exists('transport', $ravenOptions)) {
            $transport = $ravenOptions['transport'];
            if (is_string($transport)) {
                $transport = $container->get($transport);
            }
            $ravenOptions['transport'] = $transport;
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
     *
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
