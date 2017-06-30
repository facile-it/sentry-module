<?php

namespace Facile\SentryModuleTest\Service;

use Facile\Sentry\Common\StackTrace\StackTrace;
use Facile\SentryModule\Options\ConfigurationInterface;
use Facile\SentryModule\Options\StackTraceOptionsInterface;
use Facile\SentryModule\Service\StackTraceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class StackTraceFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);
        $stackTraceOptions = $this->prophesize(StackTraceOptionsInterface::class);
        $client = $this->prophesize(\Raven_Client::class);
        $serializer = $this->prophesize(\Raven_Serializer::class);
        $reprSerializer = $this->prophesize(\Raven_ReprSerializer::class);

        $configuration->getStackTraceOptions()->shouldBeCalled()->willReturn($stackTraceOptions->reveal());
        $stackTraceOptions->getIgnoreBacktraceNamespaces()->shouldBeCalled()->willReturn([]);
        $container->get(ConfigurationInterface::class)->shouldBeCalled()->willReturn($configuration->reveal());
        $container->get(\Raven_Client::class)->shouldBeCalled()->willReturn($client->reveal());
        $container->has(\Raven_Serializer::class)->willReturn(true);
        $container->has(\Raven_ReprSerializer::class)->willReturn(true);
        $container->get(\Raven_Serializer::class)->shouldBeCalled()->willReturn($serializer->reveal());
        $container->get(\Raven_ReprSerializer::class)->shouldBeCalled()->willReturn($reprSerializer->reveal());

        $factory = new StackTraceFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(StackTrace::class, $service);
    }
}
