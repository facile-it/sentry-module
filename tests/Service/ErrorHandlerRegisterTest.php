<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ErrorHandlerRegister;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class ErrorHandlerRegisterTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterHandlers()
    {
        $register = new ErrorHandlerRegister();

        $options = $this->prophesize(ClientOptions::class);
        $client = $this->prophesize(Client::class);
        $errorHandler = $this->prophesize(\Raven_ErrorHandler::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);
        $errorHandlerListener = $this->prophesize(ListenerAggregateInterface::class);

        $client->getOptions()->willReturn($options->reveal());
        $client->getErrorHandler()->willReturn($errorHandler->reveal());
        $client->getErrorHandlerListener()->willReturn($errorHandlerListener->reveal());

        $options->isRegisterErrorHandler()->willReturn(false);
        $options->isRegisterExceptionHandler()->willReturn(false);
        $options->isRegisterShutdownFunction()->willReturn(false);
        $options->isRegisterErrorListener()->willReturn(false);

        $errorHandler->registerErrorHandler()->shouldNotBeCalled();
        $errorHandler->registerExceptionHandler()->shouldNotBeCalled();
        $errorHandler->registerShutdownFunction()->shouldNotBeCalled();
        $errorHandlerListener->attach($eventManager->reveal())->shouldNotBeCalled();

        $register->registerHandlers($client->reveal(), $eventManager->reveal());
    }

    public function testRegisterHandlersWithRegisters()
    {
        $register = new ErrorHandlerRegister();

        $options = $this->prophesize(ClientOptions::class);
        $client = $this->prophesize(Client::class);
        $errorHandler = $this->prophesize(\Raven_ErrorHandler::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);
        $errorHandlerListener = $this->prophesize(ListenerAggregateInterface::class);

        $client->getOptions()->willReturn($options->reveal());
        $client->getErrorHandler()->willReturn($errorHandler->reveal());
        $client->getErrorHandlerListener()->willReturn($errorHandlerListener->reveal());

        $options->isRegisterErrorHandler()->willReturn(true);
        $options->isRegisterExceptionHandler()->willReturn(true);
        $options->isRegisterShutdownFunction()->willReturn(true);
        $options->isRegisterErrorListener()->willReturn(true);
        $options->getErrorHandlerListenerPriority()->willReturn(100);

        $errorHandler->registerErrorHandler()->shouldBeCalled();
        $errorHandler->registerExceptionHandler()->shouldBeCalled();
        $errorHandler->registerShutdownFunction()->shouldBeCalled();
        $errorHandlerListener->attach($eventManager->reveal(), 100)->shouldBeCalled();

        $register->registerHandlers($client->reveal(), $eventManager->reveal());
    }
}
