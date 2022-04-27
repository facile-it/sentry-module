<?php

declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Sentry\State\HubInterface;

final class ErrorHandlerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
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
