<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Service\ClientInterface;

class ClientAwareTraitStubTest extends \PHPUnit\Framework\TestCase
{
    public function testGetterAndSetter()
    {
        $client = $this->prophesize(ClientInterface::class);

        $stub = new ClientAwareTraitStub();
        $stub->setClient($client->reveal());

        static::assertSame($client->reveal(), $stub->getClient());
    }
}
