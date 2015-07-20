<?php

namespace Facile\SentryModule\Options;

use RuntimeException;
use Zend\Stdlib\AbstractOptions;

/**
 * Class OptionsParser
 */
class OptionsParser
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $optionsClass;

    /**
     * @param array $config
     * @param $type
     * @param $name
     * @param $optionsClass
     */
    public function __construct(array $config, $type, $name, $optionsClass)
    {
        $this->config = $config;
        $this->type = $type;
        $this->name = $name;
        $this->optionsClass = $optionsClass;
    }

    /**
     * @return AbstractOptions
     * @throws RuntimeException
     */
    public function getOptions()
    {
        $options = $this->config['sentry'];
        $options = isset($options[$this->type][$this->name]) ? $options[$this->type][$this->name] : null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf(
                    'Option "%s" not found for "sentry.%s"',
                    $this->name,
                    $this->type
                )
            );
        }

        return new $this->optionsClass($options);
    }
}
