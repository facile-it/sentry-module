<?php

namespace Facile\SentryModule\Options;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Zend\Stdlib\AbstractOptions;

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
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
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
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegisterExceptionHandler()
    {
        return $this->registerExceptionHandler;
    }

    /**
     * @param boolean $registerExceptionHandler
     * @return $this
     */
    public function setRegisterExceptionHandler($registerExceptionHandler)
    {
        $this->registerExceptionHandler = $registerExceptionHandler;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegisterErrorHandler()
    {
        return $this->registerErrorHandler;
    }

    /**
     * @param boolean $registerErrorHandler
     * @return $this
     */
    public function setRegisterErrorHandler($registerErrorHandler)
    {
        $this->registerErrorHandler = $registerErrorHandler;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegisterShutdownFunction()
    {
        return $this->registerShutdownFunction;
    }

    /**
     * @param boolean $registerShutdownFunction
     * @return $this
     */
    public function setRegisterShutdownFunction($registerShutdownFunction)
    {
        $this->registerShutdownFunction = $registerShutdownFunction;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegisterErrorListener()
    {
        return $this->registerErrorListener;
    }

    /**
     * @param boolean $registerErrorListener
     * @return $this
     */
    public function setRegisterErrorListener($registerErrorListener)
    {
        $this->registerErrorListener = $registerErrorListener;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorHandlerListener()
    {
        return $this->errorHandlerListener;
    }

    /**
     * @param string $errorHandlerListener
     * @return $this
     */
    public function setErrorHandlerListener($errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;
        return $this;
    }
}
