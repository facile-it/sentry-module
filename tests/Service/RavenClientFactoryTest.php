<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ConfigurationInterface;
use Facile\SentryModule\Service\RavenClientFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class RavenClientFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);

        $container->get(ConfigurationInterface::class)->shouldBeCalled()->willReturn($configuration->reveal());
        $configuration->getDsn()->shouldBeCalled()->willReturn('http://user:pass@host/1');
        $configuration->getRavenOptions()->shouldBeCalled()->willReturn([]);

        $factory = new RavenClientFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(\Raven_Client::class, $service);
    }
}
