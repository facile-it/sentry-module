<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Service\Client;

class ClientAwareTraitStubTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAndSetter()
    {
        $client = $this->prophesize(Client::class);

        $stub = new ClientAwareTraitStub();
        $stub->setClient($client->reveal());

        static::assertSame($client->reveal(), $stub->getClient());
    }
}
