<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Processor\SanitizeDataProcessor;
use Interop\Container\ContainerInterface;

/**
 * Class ClientFactory.
 */
class ClientFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return Client
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $optionsArray */
        $optionsArray = $container->get('config')['facile']['sentry']['client'][$this->name];

        $options = new ClientOptions($optionsArray);

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
}
