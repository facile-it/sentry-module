<?php

declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Sentry\State\HubInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

final class ErrorHandlerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /** @var HubInterface */
    private $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleError'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleError'], $priority);
    }

    public function handleError(MvcEvent $event): void
    {
        $exception = $event->getParam('exception');
        if (! $exception instanceof \Throwable) {
            return;
        }

        $this->hub->captureException($exception);
    }
}
