<?php

declare(strict_types=1);

namespace Facile\SentryModule\Log\Writer;

use Facile\SentryModule\Service\ClientInterface;
use Traversable;
use Zend\Log\Writer\AbstractWriter;
use Zend\Log\Logger;
use Raven_Client;
use Facile\SentryModule\Exception;

/**
 * Class Sentry.
 */
final class Sentry extends AbstractWriter
{
    /**
     * @var ClientInterface
     */
    private $client;

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
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     */
    public function __construct(array $options = null)
    {
        parent::__construct($options);

        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (! is_array($options) || ! array_key_exists('client', $options) || ! $options['client'] instanceof ClientInterface) {
            throw new Exception\InvalidArgumentException('No client specified in options');
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
        if ($extra instanceof Traversable) {
            $extra = iterator_to_array($extra);
        } elseif (! is_array($extra)) {
            $extra = [];
        }

        if ($this->contextContainsException($extra)) {
            /** @var \Exception $exception */
            $exception = $extra['exception'];
            unset($extra['exception']);

            $data = [
                'extra' => $this->sanitizeContextData($extra),
                'level' => $priority,
            ];

            if ($event['message'] !== $exception->getMessage()) {
                $data['message'] = sprintf('%s :: %s', $event['message'], $exception->getMessage());
            }

            $this->client->getRaven()->captureException(
                $exception,
                $data
            );

            return;
        }

        $stack = isset($extra['stack']) && is_array($extra['stack']) && count($extra['stack'])
            ? $extra['stack'] : false;

        $this->client->getRaven()->captureMessage(
            $event['message'],
            [],
            [
                'extra' => $this->sanitizeContextData($extra),
                'level' => $priority,
            ],
            $stack
        );
    }

    /**
     * @param array $context
     *
     * @return array
     */
    protected function sanitizeContextData(array $context): array
    {
        array_walk_recursive($context, [$this, 'sanitizeContextItem']);

        return $context;
    }

    /**
     * @param mixed $value
     */
    protected function sanitizeContextItem(&$value)
    {
        if ($value instanceof Traversable) {
            $value = iterator_to_array($value);
        }

        if (is_array($value)) {
            $value = $this->sanitizeContextData($value);
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
    protected function objectIsThrowable($object): bool
    {
        return $object instanceof \Throwable || $object instanceof \Exception;
    }

    /**
     * @param array $context
     *
     * @return bool
     */
    protected function contextContainsException(array $context): bool
    {
        if (! array_key_exists('exception', $context)) {
            return false;
        }

        return $this->objectIsThrowable($context['exception']);
    }
}
