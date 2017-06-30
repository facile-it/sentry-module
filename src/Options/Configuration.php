<?php

declare(strict_types=1);

namespace Facile\SentryModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ConfigurationOptions.
 */
class Configuration extends AbstractOptions implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $dsn = '';
    /**
     * @var array
     */
    private $ravenOptions = [];
    /**
     * @var string
     */
    private $ravenJavascriptDsn = '';
    /**
     * @var string
     */
    private $ravenJavascriptUri = 'https://cdn.ravenjs.com/3.16.0/raven.min.js';
    /**
     * @var array
     */
    private $ravenJavascriptOptions = [];
    /**
     * @var bool
     */
    private $injectRavenJavascript = false;
    /**
     * @var ErrorHandlerOptionsInterface
     */
    private $errorHandlerOptions;
    /**
     * @var StackTraceOptionsInterface
     */
    private $stackTraceOptions;

    /**
     * {@inheritDoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->errorHandlerOptions = $this->errorHandlerOptions ?: new ErrorHandlerOptions();
        $this->stackTraceOptions = $this->stackTraceOptions ?: new StackTraceOptions();
    }

    /**
     * @return ErrorHandlerOptionsInterface
     */
    public function getErrorHandlerOptions(): ErrorHandlerOptionsInterface
    {
        return $this->errorHandlerOptions;
    }

    /**
     * @param ErrorHandlerOptionsInterface $errorHandlerOptions
     */
    public function setErrorHandlerOptions(ErrorHandlerOptionsInterface $errorHandlerOptions)
    {
        $this->errorHandlerOptions = $errorHandlerOptions;
    }

    /**
     * @return StackTraceOptionsInterface
     */
    public function getStackTraceOptions(): StackTraceOptionsInterface
    {
        return $this->stackTraceOptions;
    }

    /**
     * @param StackTraceOptionsInterface $stackTraceOptions
     */
    public function setStackTraceOptions(StackTraceOptionsInterface $stackTraceOptions)
    {
        $this->stackTraceOptions = $stackTraceOptions;
    }

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
    public function getRavenOptions(): array
    {
        return $this->ravenOptions;
    }

    /**
     * @param array $ravenOptions
     */
    public function setRavenOptions(array $ravenOptions)
    {
        $this->ravenOptions = $ravenOptions;
    }

    /**
     * @return string
     */
    public function getRavenJavascriptDsn(): string
    {
        return $this->ravenJavascriptDsn;
    }

    /**
     * @param string $ravenJavascriptDsn
     */
    public function setRavenJavascriptDsn(string $ravenJavascriptDsn)
    {
        $this->ravenJavascriptDsn = $ravenJavascriptDsn;
    }

    /**
     * @return string
     */
    public function getRavenJavascriptUri(): string
    {
        return $this->ravenJavascriptUri;
    }

    /**
     * @param string $ravenJavascriptUri
     */
    public function setRavenJavascriptUri(string $ravenJavascriptUri)
    {
        $this->ravenJavascriptUri = $ravenJavascriptUri;
    }

    /**
     * @return array
     */
    public function getRavenJavascriptOptions(): array
    {
        return $this->ravenJavascriptOptions;
    }

    /**
     * @param array $ravenJavascriptOptions
     */
    public function setRavenJavascriptOptions(array $ravenJavascriptOptions)
    {
        $this->ravenJavascriptOptions = $ravenJavascriptOptions;
    }

    /**
     * @return bool
     */
    public function shouldInjectRavenJavascript(): bool
    {
        return $this->injectRavenJavascript;
    }

    /**
     * @param bool $injectRavenJavascript
     */
    public function setInjectRavenJavascript(bool $injectRavenJavascript)
    {
        $this->injectRavenJavascript = $injectRavenJavascript;
    }
}
