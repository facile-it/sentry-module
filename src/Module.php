<?php

namespace Facile\SentryModule;

use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ErrorHandlerRegister;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 */
class Module implements ConfigProviderInterface, BootstrapListenerInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var MvcEvent $e */
        $application = $e->getApplication();
        $container = $application->getServiceManager();
        $moduleConfig = $container->get('config')['facile']['sentry'];
        $clients = array_keys($moduleConfig['client']);

        $errorHandlerRegister = $container->get(ErrorHandlerRegister::class);
        
        foreach ($clients as $serviceKey) {
            $serviceName = sprintf('facile.sentry.client.%s', $serviceKey);
            
            /** @var Client $raven */
            $client = $container->get($serviceName);
            $errorHandlerRegister->registerHandlers($client, $application->getEventManager());
        }
    }
}
