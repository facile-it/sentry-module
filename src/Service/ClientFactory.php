<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Processor\SanitizeDataProcessor;
use Facile\SentryModule\SendCallback\CallbackChain;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientFactory.
 */
final class ClientFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface    $container
     * @param array|string|callable $callbackOptions
     *
     * @return CallbackChain
     */
    protected function buildCallbackChain(ContainerInterface $container, $callbackOptions): CallbackChain
    {
        $callbackChain = new CallbackChain();

        if (null === $callbackOptions) {
            return $callbackChain;
        }

        if (is_callable($callbackOptions) || is_string($callbackOptions)) {
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Client
    {
        /** @var ClientOptions $clientOptions */
        $clientOptions = $this->getOptions($container, 'client');

        $ravenOptions = $clientOptions->getOptions();

        if (! array_key_exists('processors', $ravenOptions)) {
            $ravenOptions['processors'] = [SanitizeDataProcessor::class];
        }

        if (! array_key_exists('logger', $ravenOptions)) {
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

        $ravenClient = new \Raven_Client($clientOptions->getDsn(), $ravenOptions);

        $client = new Client($ravenClient, $clientOptions);

        $errorHandlerListener = $container->get($clientOptions->getErrorHandlerListener());
        if ($errorHandlerListener instanceof ClientAwareInterface) {
            $errorHandlerListener->setClient($client);
        }
        // @todo: find another way to handle this. This should be passed in the constructor.
        $client->setErrorHandlerListener($errorHandlerListener);

        return $client;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Client
    {
        return $this($serviceLocator, Client::class);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     *
     * @return string
     */
    public function getOptionsClass(): string
    {
        return ClientOptions::class;
    }
}
