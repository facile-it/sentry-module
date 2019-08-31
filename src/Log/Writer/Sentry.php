<?php

declare(strict_types=1);

namespace Facile\SentryModule\Log\Writer;

use Facile\SentryModule\Exception;
use Sentry\Severity;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Traversable;
use Zend\Log\Logger;
use Zend\Log\Writer\AbstractWriter;

final class Sentry extends AbstractWriter
{
    /** @var HubInterface|null */
    private $hub;

    private function getSeverityFromLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::DEBUG:
                return Severity::debug();
            case Logger::WARN:
                return Severity::warning();
            case Logger::ERR:
                return Severity::error();
            case Logger::CRIT:
            case Logger::ALERT:
            case Logger::EMERG:
                return Severity::fatal();
            default:
                return Severity::info();
        }
    }

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
            $options = \iterator_to_array($options);
        }

        $hub = $options['hub'] ?? null;

        if (null !== $hub && ! $hub instanceof HubInterface) {
            throw new Exception\InvalidArgumentException('Invalid Sentry Hub');
        }

        $this->hub = $hub;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event log data event
     */
    protected function doWrite(array $event): void
    {
        $hub = $this->hub ?: Hub::getCurrent();

        $context = $event['extra'] ?? [];

        if ($context instanceof Traversable) {
            $context = \iterator_to_array($context);
        } elseif (! \is_array($context)) {
            $context = [];
        }

        $payload = [
            'level' => $this->getSeverityFromLevel($event['priority']),
            'message' => $event['message'],
        ];

        $exception = $context['exception'] ?? null;

        if ($exception instanceof \Throwable) {
            $payload['exception'] = $exception;
            unset($context['exception']);
        }

        $hub->withScope(static function (Scope $scope) use ($hub, $event, $context, $payload): void {
            $scope->setExtra('zend.priority', $event['priority']);

            foreach ($context as $key => $value) {
                $scope->setExtra((string) $key, $value);
            }

            $hub->captureEvent($payload);
        });
    }
}
