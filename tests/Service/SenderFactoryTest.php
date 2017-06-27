<?php

namespace Facile\SentryModuleTest\Service;

use Facile\Sentry\Common\Sanitizer\SanitizerInterface;
use Facile\Sentry\Common\Sender\Sender;
use Facile\Sentry\Common\StackTrace\StackTraceInterface;
use Facile\SentryModule\Service\SenderFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class SenderFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $client = $this->prophesize(\Raven_Client::class);
        $sanitizer = $this->prophesize(SanitizerInterface::class);
        $stackTrace = $this->prophesize(StackTraceInterface::class);

        $container->get(\Raven_Client::class)->shouldBeCalled()->willReturn($client->reveal());
        $container->get(SanitizerInterface::class)->shouldBeCalled()->willReturn($sanitizer->reveal());
        $container->get(StackTraceInterface::class)->shouldBeCalled()->willReturn($stackTrace->reveal());

        $factory = new SenderFactory();

        /** @var Sender $service */
        $service = $factory($container->reveal());

        $this->assertInstanceOf(Sender::class, $service);
        $this->assertSame($client->reveal(), $service->getClient());
        $this->assertSame($sanitizer->reveal(), $service->getSanitizer());
        $this->assertSame($stackTrace->reveal(), $service->getStackTrace());
    }
}
