<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Log\Writer\SentryFactory;
use Facile\SentryModule\Service\Client;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class SentryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $client = $this->prophesize(Client::class);

        $pluginManager->getServiceLocator()->willReturn($container->reveal());

        $container->get('client')->shouldBeCalledTimes(1)->willReturn($client->reveal());

        $factory = new SentryFactory();
        $factory->setCreationOptions(['client' => 'client']);
        $service = $factory->createService($pluginManager->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFactoryWithNoClient()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);

        $factory = new SentryFactory();
        $service = $factory->createService($container->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }
}
