<?php

namespace Facile\SentryModuleTest\Listener\Listener;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Service\Client;
use Prophecy\Argument;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class ErrorHandlerListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testGettersAndSetters()
    {
        $client = $this->prophesize(Client::class);

        $listener = new ErrorHandlerListener($client->reveal());

        static::assertEquals([], $listener->getNoCatchExceptions());

        $listener->setNoCatchExceptions(['foo']);

        static::assertEquals(['foo'], $listener->getNoCatchExceptions());
    }

    public function testAttach()
    {
        $client = $this->prophesize(Client::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);

        $listener = new ErrorHandlerListener($client->reveal());

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();

        $listener->attach($eventManager->reveal(), -100);
    }

    public function testHandleError()
    {
        $exception = $this->prophesize(\Exception::class);
        $raven = $this->prophesize(\Raven_Client::class);
        $client = $this->prophesize(Client::class);
        $event = $this->prophesize(MvcEvent::class);

        $event->getParam('exception')->willReturn($exception->reveal());
        $raven->captureException($exception->reveal())->shouldBeCalled();
        $client->getRaven()->willReturn($raven->reveal());

        $listener = new ErrorHandlerListener($client->reveal());

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithInvalidException()
    {
        $raven = $this->prophesize(\Raven_Client::class);
        $client = $this->prophesize(Client::class);
        $event = $this->prophesize(MvcEvent::class);

        $event->getParam('exception')->willReturn(new \stdClass());
        $raven->captureException(Argument::any())->shouldNotBeCalled();
        $client->getRaven()->willReturn($raven->reveal());

        $listener = new ErrorHandlerListener($client->reveal());

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithNoCatchException()
    {
        $raven = $this->prophesize(\Raven_Client::class);
        $client = $this->prophesize(Client::class);
        $event = $this->prophesize(MvcEvent::class);

        $event->getParam('exception')->willReturn(new \LogicException());
        $raven->captureException(Argument::any())->shouldNotBeCalled();
        $client->getRaven()->willReturn($raven->reveal());

        $listener = new ErrorHandlerListener($client->reveal());
        $listener->setNoCatchExceptions([
            \LogicException::class
        ]);

        $listener->handleError($event->reveal());
    }
}
