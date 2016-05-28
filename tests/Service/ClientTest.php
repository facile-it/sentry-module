<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Service\Client;
use Zend\EventManager\ListenerAggregateInterface;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $raven = $this->prophesize(\Raven_Client::class);
        $options = $this->prophesize(ClientOptions::class);
        $errorHandler = $this->prophesize(\Raven_ErrorHandler::class);
        $errorHandlerListener = $this->prophesize(ListenerAggregateInterface::class);
        $client = new Client($raven->reveal(), $options->reveal(), $errorHandler->reveal());
        $client->setErrorHandlerListener($errorHandlerListener->reveal());

        static::assertSame($raven->reveal(), $client->getRaven());
        static::assertSame($options->reveal(), $client->getOptions());
        static::assertSame($errorHandler->reveal(), $client->getErrorHandler());
        static::assertSame($errorHandlerListener->reveal(), $client->getErrorHandlerListener());
    }

    public function testConstructorWithMinimumArgs()
    {
        $raven = $this->prophesize(\Raven_Client::class);
        $options = $this->prophesize(ClientOptions::class);
        $client = new Client($raven->reveal(), $options->reveal());

        static::assertSame($raven->reveal(), $client->getRaven());
        static::assertSame($options->reveal(), $client->getOptions());
        static::assertInstanceOf(\Raven_ErrorHandler::class, $client->getErrorHandler());
        static::assertInstanceOf(ListenerAggregateInterface::class, $client->getErrorHandlerListener());
    }
}
