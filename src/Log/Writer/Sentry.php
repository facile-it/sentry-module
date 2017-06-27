<?php

declare(strict_types=1);

namespace Facile\SentryModule\Log\Writer;

use Facile\Sentry\Common\Sender\SenderInterface;
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
     * @var SenderInterface
     */
    private $sender;

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
     * @param array|Traversable $options
     *
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (! is_array($options) || ! array_key_exists('sender', $options) || ! $options['sender'] instanceof SenderInterface) {
            throw new Exception\InvalidArgumentException('No sender specified in options');
        }

        $this->sender = $options['sender'];
    }

    /**
     * Write a message to the log.
     *
     * @param array $event log data event
     */
    protected function doWrite(array $event)
    {
        $priority = $this->priorityMap[$event['priority']];
        $message = $event['message'];
        $context = $event['extra'] ?? [];

        if ($context instanceof Traversable) {
            $context = iterator_to_array($context);
        } elseif (! is_array($context)) {
            $context = [];
        }

        $this->sender->send($priority, (string) $message, $context);
    }
}
