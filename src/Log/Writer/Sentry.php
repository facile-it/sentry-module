<?php

declare(strict_types=1);

namespace Facile\SentryModule\Log\Writer;

use Facile\SentryModule\Exception;
use Laminas\Log\Logger;
use Laminas\Log\Writer\AbstractWriter;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\SentrySdk;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Throwable;
use Traversable;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class Sentry extends AbstractWriter
{
    private ?HubInterface $hub;

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
     * @param null|array<mixed>|Traversable<array-key, mixed> $options
     *
     * @throws Exception\InvalidArgumentException
     *
     * @psalm-suppress InvalidArgument
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
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
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @param array<string, mixed> $event log data event
     *
     * @psalm-param array{message: string, priority: int, extra?: array<string, mixed>|Traversable<string, mixed>} $event
     */
    protected function doWrite(array $event): void
    {
        $hub = $this->hub ?: SentrySdk::getCurrentHub();

        /** @var array<string, mixed>|Traversable<string, mixed>|null $context */
        $context = $event['extra'] ?? [];

        if ($context instanceof Traversable) {
            $context = iterator_to_array($context);
        } elseif (! \is_array($context)) {
            /** @var array<string, mixed> $context */
            $context = [];
        }

        $hub->withScope(function (Scope $scope) use ($hub, $event, $context): void {
            $scope->setExtra('laminas.priority', $event['priority']);

            $hints = [];

            /** @var mixed $exception */
            $exception = $context['exception'] ?? null;

            if ($exception instanceof Throwable) {
                $hints['exception'] = $exception;
                unset($context['exception']);
            }

            $hints['extra'] = $context;
            $level = $this->getSeverityFromLevel($event['priority']);

            /**
             * @var string|int $key
             * @var mixed $value
             */
            foreach ($context as $key => $value) {
                $scope->setExtra((string) $key, $value);
            }

            $sentryEvent = Event::createEvent();
            $sentryEvent->setMessage($event['message']);
            $sentryEvent->setLevel($level);

            $hub->captureEvent($sentryEvent, EventHint::fromArray($hints));
        });
    }
}
