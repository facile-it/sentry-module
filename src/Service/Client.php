<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Raven_Client;
use Raven_ErrorHandler;
use Facile\SentryModule\Options\ClientOptions;
use Zend\EventManager\ListenerAggregateInterface;

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
     * @return Raven_Client
     */
    public function getRaven()
    {
        return $this->raven;
    }

    /**
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
     * @return ClientOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
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
     * @param ListenerAggregateInterface $errorHandlerListener
     * @return $this
     */
    public function setErrorHandlerListener(ListenerAggregateInterface $errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;
        return $this;
    }
}
