<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Listener;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sentry\EventId;
use Sentry\State\HubInterface;

class ErrorHandlerListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testAttach(): void
    {
        $hub = $this->prophesize(HubInterface::class);

        $listener = new ErrorHandlerListener($hub->reveal());
        $eventManager = $this->prophesize(EventManagerInterface::class);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$listener, 'handleError'], -100)
            ->shouldBeCalled();

        $listener->attach($eventManager->reveal(), -100);
    }

    public function testHandleErrorWithException(): void
    {
        $exception = $this->prophesize(\Exception::class);
        $event = $this->prophesize(MvcEvent::class);
        $hub = $this->prophesize(HubInterface::class);

        $listener = new ErrorHandlerListener($hub->reveal());

        $event->getParam('exception')->willReturn($exception->reveal());
        $hub->captureException($exception->reveal())
            ->shouldBeCalled()
            ->willReturn(EventId::generate());

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithError(): void
    {
        $exception = $this->prophesize(\Error::class);
        $event = $this->prophesize(MvcEvent::class);
        $hub = $this->prophesize(HubInterface::class);

        $listener = new ErrorHandlerListener($hub->reveal());

        $event->getParam('exception')->willReturn($exception->reveal());
        $hub->captureException($exception->reveal())
            ->shouldBeCalled()
            ->willReturn(EventId::generate());

        $listener->handleError($event->reveal());
    }

    public function testHandleErrorWithInvalidException(): void
    {
        $exception = $this->prophesize(\stdClass::class);
        $event = $this->prophesize(MvcEvent::class);
        $hub = $this->prophesize(HubInterface::class);

        $listener = new ErrorHandlerListener($hub->reveal());

        $event->getParam('exception')->willReturn($exception->reveal());
        $hub->captureException(Argument::cetera())->shouldNotBeCalled();

        $listener->handleError($event->reveal());
    }
}
