<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Raven_Client;
use Raven_ErrorHandler;
use Facile\SentryModule\Options\ClientOptions;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Class Client.
 */
class Client
{
    /**
     * @var Raven_Client
     */
    protected $raven;
    /**
     * @var Raven_ErrorHandler
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
     * @param Raven_ErrorHandler $errorHandler
     */
    public function __construct(Raven_Client $raven, ClientOptions $options, Raven_ErrorHandler $errorHandler = null)
    {
        $this->raven = $raven;
        $this->options = $options;
        $this->errorHandler = $errorHandler;
    }

    /**
     * Get the Raven client.
     *
     * @return Raven_Client
     */
    public function getRaven()
    {
        return $this->raven;
    }

    /**
     * Get the Raven error handler.
     *
     * @return Raven_ErrorHandler
     */
    public function getErrorHandler()
    {
        if (!$this->errorHandler) {
            $this->errorHandler = new Raven_ErrorHandler($this->getRaven());
        }

        return $this->errorHandler;
    }

    /**
     * @param Raven_ErrorHandler $errorHandler
     *
     * @return $this
     */
    public function setErrorHandler(Raven_ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /**
     * Get the client options.
     *
     * @return ClientOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the error handler listener.
     *
     * @return ListenerAggregateInterface
     */
    public function getErrorHandlerListener()
    {
        if (!$this->errorHandlerListener) {
            $this->errorHandlerListener = new ErrorHandlerListener($this);
        }

        return $this->errorHandlerListener;
    }

    /**
     * Set the error handler listener.
     *
     * @param ListenerAggregateInterface $errorHandlerListener
     *
     * @return $this
     */
    public function setErrorHandlerListener(ListenerAggregateInterface $errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;

        return $this;
    }
}
