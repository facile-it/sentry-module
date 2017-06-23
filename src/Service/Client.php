<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Raven_Client;
use Raven_ErrorHandler;
use Facile\SentryModule\Options\ClientOptions;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Class Client.
 */
final class Client implements ClientInterface
{
    /**
     * @var Raven_Client
     */
    protected $raven;
    /**
     * @var null|Raven_ErrorHandler
     */
    protected $errorHandler;
    /**
     * @var ListenerAggregateInterface
     */
    protected $errorHandlerListener;
    /**
     * @var ClientOptions
     */
    protected $options;

    /**
     * Client constructor.
     *
     * @param Raven_Client       $raven
     * @param ClientOptions      $options
     * @param Raven_ErrorHandler|null $errorHandler
     * @param ErrorHandlerListener|null $errorHandlerListener
     */
    public function __construct(
        Raven_Client $raven,
        ClientOptions $options,
        Raven_ErrorHandler $errorHandler = null,
        ErrorHandlerListener $errorHandlerListener = null
    ) {
        $this->raven = $raven;
        $this->options = $options;
        $this->errorHandler = $errorHandler ?: new Raven_ErrorHandler($raven);
        $this->errorHandlerListener = $errorHandlerListener ?: new ErrorHandlerListener($this);
    }

    /**
     * Get the Raven client.
     *
     * @return Raven_Client
     */
    public function getRaven(): Raven_Client
    {
        return $this->raven;
    }

    /**
     * Get the Raven error handler.
     *
     * @return Raven_ErrorHandler
     */
    public function getErrorHandler(): Raven_ErrorHandler
    {
        return $this->errorHandler;
    }

    /**
     * Get the client options.
     *
     * @return ClientOptions
     */
    public function getOptions(): ClientOptions
    {
        return $this->options;
    }

    /**
     * Get the error handler listener.
     *
     * @return ListenerAggregateInterface
     */
    public function getErrorHandlerListener(): ListenerAggregateInterface
    {
        return $this->errorHandlerListener;
    }

    /**
     * Set the error handler listener.
     *
     * @param ListenerAggregateInterface $errorHandlerListener
     */
    public function setErrorHandlerListener(ListenerAggregateInterface $errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;
    }
}
