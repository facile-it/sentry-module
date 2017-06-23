<?php

declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Facile\SentryModule\Service\ClientInterface;
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
     * @var array
     */
    protected $noCatchExceptions = [];

    /**
     * ErrorHandlerListener constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        if ($client) {
            $this->setClient($client);
        }
    }

    /**
     * @return array
     */
    public function getNoCatchExceptions(): array
    {
        return $this->noCatchExceptions;
    }

    /**
     * @param array $noCatchExceptions
     */
    public function setNoCatchExceptions(array $noCatchExceptions)
    {
        $this->noCatchExceptions = $noCatchExceptions;
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
        if (! $exception instanceof \Throwable) {
            return;
        }

        if (in_array(get_class($exception), $this->noCatchExceptions, true)) {
            return;
        }

        $this->getClient()->getRaven()->captureException($exception);
    }
}
