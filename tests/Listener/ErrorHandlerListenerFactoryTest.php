<?php

namespace Facile\SentryModuleTest\Listener;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Listener\ErrorHandlerListenerFactory;
use Facile\SentryModule\Options\ConfigurationInterface;
use Facile\SentryModule\Options\ErrorHandlerOptionsInterface;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ErrorHandlerListenerFactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $client = $this->prophesize(\Raven_Client::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);
        $errorHandlerOptions = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $container->get(\Raven_Client::class)->shouldBeCalled()->willReturn($client->reveal());
        $container->get(ConfigurationInterface::class)->shouldBeCalled()->willReturn($configuration->reveal());
        $configuration->getErrorHandlerOptions()->shouldBeCalled()->willReturn($errorHandlerOptions->reveal());

        $factory = new ErrorHandlerListenerFactory();
        $result = $factory($container->reveal());

        $this->assertInstanceOf(ErrorHandlerListener::class, $result);
    }
}
