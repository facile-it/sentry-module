<?php

declare(strict_types=1);

namespace Facile\SentryModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ConfigurationOptions.
 */
class ConfigurationOptions extends AbstractOptions
{
    /**
     * @var string
     */
    private $ravenJavascriptDsn = '';
    /**
     * @var string
     */
    private $ravenJavascriptUri = 'https://cdn.ravenjs.com/3.7.0/raven.min.js';
    /**
     * @var array
     */
    private $ravenJavascriptOptions = [];
    /**
     * @var bool
     */
    private $injectRavenJavascript = false;

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
    public function isInjectRavenJavascript(): bool
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
