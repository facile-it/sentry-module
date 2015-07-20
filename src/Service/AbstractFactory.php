<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\OptionsProviderInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractFactory implements FactoryInterface
{
    protected $options;

    public function __construct(AbstractOptions $options)
    {
        $this->options = $options;
    }

    /**
     * @return AbstractOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
}
