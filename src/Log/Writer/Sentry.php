<?php

namespace Facile\SentryModule\Log\Writer;

use Facile\SentryModule\Service\Client;
use Zend\Log\Writer\AbstractWriter;
use Zend\Log\Logger;
use Raven_Client;

/**
 * Class Sentry.
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
     *
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
     * Write a message to the log.
     *
     * @param array $event log data event
     */
    protected function doWrite(array $event)
    {
        $priority = $this->priorityMap[$event['priority']];

        $extra = $event['extra'];
        if ($extra instanceof \Traversable) {
            $extra = iterator_to_array($extra);
        } elseif (!is_array($extra)) {
            $extra = [];
        }

        if ($this->contextContainsException($extra)) {
            /** @var \Throwable $exception */
            $exception = $extra['exception'];
            unset($extra['exception']);

            if ($event['message'] !== $exception->getMessage()) {
                $exception = new ContextException($event['message'], $exception->getCode(), $exception);
            }

            $this->client->getRaven()->captureException(
                $exception,
                [
                    'extra' => $this->sanitizeContextData($extra),
                    'level' => $priority,
                ]
            );

            return;
        }

        $stack = isset($extra['stack']) && is_array($extra['stack']) ? $extra['stack'] : null;

        if (!$stack) {
            $stack = $this->cleanBacktrace(debug_backtrace());
            if (!count($stack)) {
                $stack = false;
            }
        }

        $this->client->getRaven()->captureMessage(
            $event['message'],
            $this->sanitizeContextData($extra),
            $priority,
            $stack
        );
    }

    /**
     * Remove first backtrace items until it founds something different from loggers
     *
     * @param array $backtrace
     * @return array
     */
    protected function cleanBacktrace(array $backtrace)
    {
        $excludeNamespaces = [
            'Facile\SentryModule\Log\\',
            'Psr\Log\\',
            'Zend\Log\\'
        ];

        $lastItem = null;
        while (count($backtrace)) {
            $item = $backtrace[0];
            if (!array_key_exists('class', $item)) {
                break;
            }
            $exclude = false;
            foreach ($excludeNamespaces as $namespace) {
                if (0 === strpos($item['class'], $namespace)) {
                    $exclude = true;
                    break;
                }
            }
            if (!$exclude) {
                break;
            }

            $lastItem = array_shift($backtrace);
        };

        if ($lastItem) {
            array_unshift($backtrace, $lastItem);
        }

        return $backtrace;
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

    /**
     * @param mixed $object
     *
     * @return bool
     */
    protected function objectIsThrowable($object)
    {
        return $object instanceof \Throwable || $object instanceof \Exception;
    }

    /**
     * @param array $context
     *
     * @return bool
     */
    protected function contextContainsException(array $context)
    {
        if (!array_key_exists('exception', $context)) {
            return false;
        }

        return $this->objectIsThrowable($context['exception']);
    }
}
