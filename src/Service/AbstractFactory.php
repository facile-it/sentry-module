<?php

namespace Facile\SentryModule\Service;

use Zend\ServiceManager\FactoryInterface;

abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
}
