<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Log\Writer\SentryFactory;
use Facile\SentryModule\Service\ClientInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceManager;

class SentryFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $container = $this->prophesize(ServiceManager::class);
        $client = $this->prophesize(ClientInterface::class);

        $pluginManager->getServiceLocator()->willReturn($container->reveal());

        $container->get('client')->shouldBeCalledTimes(1)->willReturn($client->reveal());

        $factory = new SentryFactory();
        $factory->setCreationOptions(['client' => 'client']);
        $service = $factory->createService($pluginManager->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }

    public function testFactoryWithNoClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $container = $this->prophesize(ServiceManager::class);

        $factory = new SentryFactory();
        $service = $factory->createService($container->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }
}
