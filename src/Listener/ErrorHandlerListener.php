<?php

declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Facile\SentryModule\Options\ErrorHandlerOptionsInterface;
use Raven_Client;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

/**
 * Class ErrorHandlerListener.
 */
class ErrorHandlerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var Raven_Client
     */
    private $ravenClient;
    /**
     * @var ErrorHandlerOptionsInterface
     */
    protected $options;

    /**
     * ErrorHandlerListener constructor.
     * @param Raven_Client $client
     * @param ErrorHandlerOptionsInterface $options
     */
    public function __construct(Raven_Client $client, ErrorHandlerOptionsInterface $options)
    {
        $this->ravenClient = $client;
        $this->options = $options;
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

        if (in_array(get_class($exception), $this->options->getSkipExceptions(), true)) {
            return;
        }

        $errorTypes = (int) ($this->options->getErrorTypes() ?? error_reporting());
        if ($exception instanceof \ErrorException && 0 === ($errorTypes & $exception->getSeverity())) {
            return;
        }

        $this->ravenClient->captureException($exception);
    }
}
