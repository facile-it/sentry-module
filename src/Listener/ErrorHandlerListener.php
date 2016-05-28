<?php

namespace Facile\SentryModule\Listener;

use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ClientAwareInterface;
use Facile\SentryModule\Service\ClientAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

/**
 * Class ErrorHandlerListener.
 */
class ErrorHandlerListener implements ListenerAggregateInterface, ClientAwareInterface
{
    use ListenerAggregateTrait;
    use ClientAwareTrait;

    /**
     * ErrorHandlerListener constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        if ($client) {
            $this->setClient($client);
        }
    }

    /**
     * Attach one or more listeners.
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleError'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleError'], $priority);
    }

    /**
     * @param MvcEvent $event
     */
    public function handleError(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        if (!$exception instanceof \Exception || (class_exists('Throwable') && !$exception instanceof \Throwable)) {
            return;
        }

        $this->getClient()->getRaven()->captureException($exception);
    }
}
