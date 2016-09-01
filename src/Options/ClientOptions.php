<?php

namespace Facile\SentryModule\Options;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Zend\Stdlib\AbstractOptions;

/**
 * Class ClientOptions.
 */
class ClientOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $dsn;
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var bool
     */
    protected $registerExceptionHandler = false;
    /**
     * @var bool
     */
    protected $registerErrorHandler = false;
    /**
     * @var bool
     */
    protected $registerShutdownFunction = false;
    /**
     * @var bool
     */
    protected $registerErrorListener = false;
    /**
     * @var string
     */
    protected $errorHandlerListener = ErrorHandlerListener::class;
    /**
     * @var int
     */
    protected $errorHandlerListenerPriority = 1;

    /**
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
     *
     * @return $this
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get if the module should register the Raven exception handler.
     *
     * @return bool
     */
    public function isRegisterExceptionHandler()
    {
        return $this->registerExceptionHandler;
    }

    /**
     * Set if the module should register the Raven exception handler.
     *
     * @param bool $registerExceptionHandler
     *
     * @return $this
     */
    public function setRegisterExceptionHandler($registerExceptionHandler)
    {
        $this->registerExceptionHandler = $registerExceptionHandler;

        return $this;
    }

    /**
     * Get if the module should register the Raven error handler.
     *
     * @return bool
     */
    public function isRegisterErrorHandler()
    {
        return $this->registerErrorHandler;
    }

    /**
     * Set if the module should register the Raven error handler.
     *
     * @param bool $registerErrorHandler
     *
     * @return $this
     */
    public function setRegisterErrorHandler($registerErrorHandler)
    {
        $this->registerErrorHandler = $registerErrorHandler;

        return $this;
    }

    /**
     * Get if the module should register the Raven shutdown function.
     *
     * @return bool
     */
    public function isRegisterShutdownFunction()
    {
        return $this->registerShutdownFunction;
    }

    /**
     * Set if the module should register the Raven shutdown function.
     *
     * @param bool $registerShutdownFunction
     *
     * @return $this
     */
    public function setRegisterShutdownFunction($registerShutdownFunction)
    {
        $this->registerShutdownFunction = $registerShutdownFunction;

        return $this;
    }

    /**
     * Get if the module should register the the error handler listener for MVC event exceptions.
     *
     * @return bool
     */
    public function isRegisterErrorListener()
    {
        return $this->registerErrorListener;
    }

    /**
     * Set if the module should register the the error handler listener for MVC event exceptions.
     *
     * @param bool $registerErrorListener
     *
     * @return $this
     */
    public function setRegisterErrorListener($registerErrorListener)
    {
        $this->registerErrorListener = $registerErrorListener;

        return $this;
    }

    /**
     * Get the error handler listener service name to register for MVC events.
     *
     * @return string Service name
     */
    public function getErrorHandlerListener()
    {
        return $this->errorHandlerListener;
    }

    /**
     * Set the error handler listener service name to register for MVC events.
     *
     * @param string $errorHandlerListener Service name
     *
     * @return $this
     */
    public function setErrorHandlerListener($errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorHandlerListenerPriority()
    {
        return $this->errorHandlerListenerPriority;
    }

    /**
     * @param int $errorHandlerListenerPriority
     *
     * @return $this
     */
    public function setErrorHandlerListenerPriority($errorHandlerListenerPriority)
    {
        $this->errorHandlerListenerPriority = $errorHandlerListenerPriority;

        return $this;
    }
}
