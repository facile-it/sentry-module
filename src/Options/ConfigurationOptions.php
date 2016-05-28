<?php

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
    protected $ravenJavascriptDsn = '';
    /**
     * @var string
     */
    protected $ravenJavascriptUri = 'https://cdn.ravenjs.com/3.0.4/raven.min.js';
    /**
     * @var bool
     */
    protected $injectRavenJavascript = false;

    /**
     * @return string
     */
    public function getRavenJavascriptDsn()
    {
        return $this->ravenJavascriptDsn;
    }

    /**
     * @param string $ravenJavascriptDsn
     *
     * @return $this
     */
    public function setRavenJavascriptDsn($ravenJavascriptDsn)
    {
        $this->ravenJavascriptDsn = $ravenJavascriptDsn;

        return $this;
    }

    /**
     * @return string
     */
    public function getRavenJavascriptUri()
    {
        return $this->ravenJavascriptUri;
    }

    /**
     * @param string $ravenJavascriptUri
     *
     * @return $this
     */
    public function setRavenJavascriptUri($ravenJavascriptUri)
    {
        $this->ravenJavascriptUri = $ravenJavascriptUri;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInjectRavenJavascript()
    {
        return $this->injectRavenJavascript;
    }

    /**
     * @param bool $injectRavenJavascript
     *
     * @return $this
     */
    public function setInjectRavenJavascript($injectRavenJavascript)
    {
        $this->injectRavenJavascript = $injectRavenJavascript;

        return $this;
    }
}
