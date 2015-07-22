<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2015 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModule\Options;

use Zend\Stdlib\AbstractOptions;

class RavenClient extends AbstractOptions
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
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}
