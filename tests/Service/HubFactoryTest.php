<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Service\HubFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Sentry\ClientInterface;

class HubFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $client = $this->prophesize(ClientInterface::class)->reveal();

        $containerProp = $this->prophesize(ContainerInterface::class);

        $containerProp->get(
            Argument::exact(ClientInterface::class)
        )->willReturn(
            $client
        );

        $hubFactory = new HubFactory();

        $hub = $hubFactory($containerProp->reveal());

        $this->assertSame($client, $hub->getClient());
    }
}
