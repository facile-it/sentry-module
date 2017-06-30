<?php

namespace Facile\SentryModuleTest\Listener\Listener;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Options\ErrorHandlerOptionsInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Raven_Client;

class ErrorHandlerListenerTest extends \PHPUnit\Framework\TestCase
{
    public function testAttach()
    {
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());
        $eventManager = $this->prophesize(EventManagerInterface::class);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();

        $listener->attach($eventManager->reveal(), -100);
    }

    public function testHandleErrorWithException()
    {
        $exception = $this->prophesize(\Exception::class);
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldBeCalled()->willReturn([]);
        $options->getErrorTypes()->shouldBeCalled()->willReturn(0);
        $event->getParam('exception')->willReturn($exception->reveal());
        $client->captureException($exception->reveal())->shouldBeCalled();

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithSkippedException()
    {
        $exception = new \InvalidArgumentException('message');
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldBeCalled()->willReturn([
            \InvalidArgumentException::class,
        ]);
        $options->getErrorTypes()->shouldNotBeCalled()->willReturn(0);
        $event->getParam('exception')->willReturn($exception);
        $client->captureException($exception)->shouldNotBeCalled();

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithError()
    {
        $exception = $this->prophesize(\Error::class);
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldBeCalled()->willReturn([]);
        $options->getErrorTypes()->shouldBeCalled()->willReturn(0);
        $event->getParam('exception')->willReturn($exception->reveal());
        $client->captureException($exception->reveal())->shouldBeCalled();

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithErrorException()
    {
        $exception = new \ErrorException('message', 0, E_WARNING);
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldBeCalled()->willReturn([]);
        $options->getErrorTypes()->shouldBeCalled()->willReturn(E_ALL);
        $event->getParam('exception')->willReturn($exception);
        $client->captureException($exception)->shouldBeCalled();

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithNotHandledErrorException()
    {
        $exception = new \ErrorException('message', 0, E_WARNING);
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldBeCalled()->willReturn([]);
        $options->getErrorTypes()->shouldBeCalled()->willReturn(E_ALL & ~E_WARNING);
        $event->getParam('exception')->willReturn($exception);
        $client->captureException($exception)->shouldNotBeCalled();

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithInvalidException()
    {
        $exception = $this->prophesize(\stdClass::class);
        $event = $this->prophesize(MvcEvent::class);
        $client = $this->prophesize(Raven_Client::class);
        $options = $this->prophesize(ErrorHandlerOptionsInterface::class);

        $listener = new ErrorHandlerListener($client->reveal(), $options->reveal());

        $options->getSkipExceptions()->shouldNotBeCalled()->willReturn([]);
        $options->getErrorTypes()->shouldNotBeCalled()->willReturn(0);
        $event->getParam('exception')->willReturn($exception->reveal());
        $client->captureException($exception->reveal())->shouldNotBeCalled();

        $listener->handleError($event->reveal());
    }
}
