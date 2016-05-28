<?php

namespace Facile\SentryModule\Service;

/**
 * Class AbstractFactory.
 */
abstract class AbstractFactory
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
