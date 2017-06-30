<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\Sentry\Common\Sender\SenderInterface;
use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Log\Writer\SentryFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceManager;

class SentryFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $container = $this->prophesize(ServiceManager::class);
        $sender = $this->prophesize(SenderInterface::class);

        $pluginManager->getServiceLocator()->willReturn($container->reveal());

        $container->get('sender')->shouldBeCalledTimes(1)->willReturn($sender->reveal());

        $factory = new SentryFactory();
        $factory->setCreationOptions(['sender' => 'sender']);
        $service = $factory->createService($pluginManager->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }

    public function testFactoryWithNoSender()
    {
        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $container = $this->prophesize(ServiceManager::class);
        $sender = $this->prophesize(SenderInterface::class);

        $pluginManager->getServiceLocator()->willReturn($container->reveal());

        $container->get(SenderInterface::class)->shouldBeCalledTimes(1)->willReturn($sender->reveal());

        $factory = new SentryFactory();
        $service = $factory->createService($pluginManager->reveal());

        static::assertInstanceOf(Sentry::class, $service);
    }
}
