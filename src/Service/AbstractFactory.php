<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Interop\Container\ContainerInterface;
use Facile\SentryModule\Exception\RuntimeException;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class AbstractFactory.
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var \Zend\Stdlib\AbstractOptions
     */
    protected $options;

    /**
     * @var string
     */
    protected $configKey = 'facile';

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ContainerInterface $container
     * @param string $key
     * @param null|string $name
     *
     * @return \Zend\Stdlib\AbstractOptions
     * @throws \Facile\SentryModule\Exception\RuntimeException
     *
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getOptions(ContainerInterface $container, $key, $name = null)
    {
        if ($name === null) {
            $name = $this->getName();
        }

        /** @var array $options */
        $options = $container->get('config');
        $options = $options[$this->configKey]['sentry'];
        $options = isset($options[$key][$name]) ? $options[$key][$name] : null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf('Options with name "%s" could not be found in "%s.%s"', $name, $this->configKey, $key)
            );
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getOptionsClass();
}
