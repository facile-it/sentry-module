<?php

declare(strict_types=1);

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
    private $dsn = '';
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var bool
     */
    private $registerExceptionHandler = false;
    /**
     * @var bool
     */
    private $registerErrorHandler = false;
    /**
     * @var bool
     */
    private $registerShutdownFunction = false;
    /**
     * @var bool
     */
    private $registerErrorListener = false;
    /**
     * @var string
     */
    private $errorHandlerListener = ErrorHandlerListener::class;
    /**
     * @var int
     */
    private $errorHandlerListenerPriority = 1;

    /**
     * @return string
     */
    public function getDsn(): string
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
     */
    public function setDsn(string $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get if the module should register the Raven exception handler.
     *
     * @return bool
     */
    public function isRegisterExceptionHandler(): bool
    {
        return $this->registerExceptionHandler;
    }

    /**
     * Set if the module should register the Raven exception handler.
     *
     * @param bool $registerExceptionHandler
     */
    public function setRegisterExceptionHandler(bool $registerExceptionHandler)
    {
        $this->registerExceptionHandler = $registerExceptionHandler;
    }

    /**
     * Get if the module should register the Raven error handler.
     *
     * @return bool
     */
    public function isRegisterErrorHandler(): bool
    {
        return $this->registerErrorHandler;
    }

    /**
     * Set if the module should register the Raven error handler.
     *
     * @param bool $registerErrorHandler
     */
    public function setRegisterErrorHandler(bool $registerErrorHandler)
    {
        $this->registerErrorHandler = $registerErrorHandler;
    }

    /**
     * Get if the module should register the Raven shutdown function.
     *
     * @return bool
     */
    public function isRegisterShutdownFunction(): bool
    {
        return $this->registerShutdownFunction;
    }

    /**
     * Set if the module should register the Raven shutdown function.
     *
     * @param bool $registerShutdownFunction
     */
    public function setRegisterShutdownFunction(bool $registerShutdownFunction)
    {
        $this->registerShutdownFunction = $registerShutdownFunction;
    }

    /**
     * Get if the module should register the the error handler listener for MVC event exceptions.
     *
     * @return bool
     */
    public function isRegisterErrorListener(): bool
    {
        return $this->registerErrorListener;
    }

    /**
     * Set if the module should register the the error handler listener for MVC event exceptions.
     *
     * @param bool $registerErrorListener
     */
    public function setRegisterErrorListener(bool $registerErrorListener)
    {
        $this->registerErrorListener = $registerErrorListener;
    }

    /**
     * Get the error handler listener service name to register for MVC events.
     *
     * @return string Service name
     */
    public function getErrorHandlerListener(): string
    {
        return $this->errorHandlerListener;
    }

    /**
     * Set the error handler listener service name to register for MVC events.
     *
     * @param string $errorHandlerListener Service name
     */
    public function setErrorHandlerListener(string $errorHandlerListener)
    {
        $this->errorHandlerListener = $errorHandlerListener;
    }

    /**
     * @return int
     */
    public function getErrorHandlerListenerPriority(): int
    {
        return $this->errorHandlerListenerPriority;
    }

    /**
     * @param int $errorHandlerListenerPriority
     */
    public function setErrorHandlerListenerPriority(int $errorHandlerListenerPriority)
    {
        $this->errorHandlerListenerPriority = $errorHandlerListenerPriority;
    }
}
