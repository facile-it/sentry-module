<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Listener;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Listener\ErrorHandlerListenerFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Sentry\State\HubInterface;

class ErrorHandlerListenerFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $hub = $this->prophesize(HubInterface::class);

        $container->get(HubInterface::class)->shouldBeCalled()->willReturn($hub->reveal());

        $factory = new ErrorHandlerListenerFactory();
        $result = $factory($container->reveal());

        $this->assertInstanceOf(ErrorHandlerListener::class, $result);
    }
}
