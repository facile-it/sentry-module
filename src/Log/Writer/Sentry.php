<?php

namespace Facile\SentryModule\Log\Writer;

use Facile\SentryModule\Service\Client;
use Zend\Log\Writer\AbstractWriter;
use Zend\Log\Logger;
use \Raven_Client;

/**
 * Class Sentry
 *
 * @package Facile\SentryModule\Log\Writer
 */
class Sentry extends AbstractWriter
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $priorityMap = [
        Logger::EMERG => Raven_Client::FATAL,
        Logger::ALERT => Raven_Client::ERROR,
        Logger::CRIT => Raven_Client::ERROR,
        Logger::ERR => Raven_Client::ERROR,
        Logger::WARN => Raven_Client::WARNING,
        Logger::NOTICE => Raven_Client::INFO,
        Logger::INFO => Raven_Client::INFO,
        Logger::DEBUG => Raven_Client::DEBUG,
    ];

    /**
     * Sentry constructor.
     *
     * @param array $options
     * @throws \RuntimeException
     * @throws \Zend\Log\Exception\InvalidArgumentException
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!array_key_exists('client', $options)) {
            throw new \RuntimeException('No client specified in options');
        }

        $this->client = $options['client'];
    }

    /**
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    protected function doWrite(array $event)
    {
        $priority = $this->priorityMap[$event['priority']];

        $this->client->getRaven()->captureMessage(
            $event['message'],
            $this->sanitizeContextData($event['extra']),
            $priority
        );
    }

    /**
     * @param array $context
     *
     * @return array
     */
    protected function sanitizeContextData(array $context)
    {
        array_walk_recursive($context, [$this, 'sanitizeContextItem']);

        return $context;
    }

    /**
     * @param mixed $value
     */
    protected function sanitizeContextItem(&$value)
    {
        if ($value instanceof \Traversable) {
            $value = $this->sanitizeContextData(iterator_to_array($value));
        }
        if (is_object($value)) {
            $value = method_exists($value, '__toString') ? (string) $value : get_class($value);
        } elseif (is_resource($value)) {
            $value = get_resource_type($value);
        }
    }
}
