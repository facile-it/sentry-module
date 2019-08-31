<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Log\Writer;

use ArrayObject;
use Facile\SentryModule\Log\Writer\Sentry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Zend\Log\Logger;

class SentryTest extends TestCase
{
    public function testWrite(): void
    {
        $hub = $this->prophesize(HubInterface::class);
        $options = new ArrayObject([
            'hub' => $hub->reveal(),
        ]);
        $writer = new Sentry($options);

        $scope = new Scope();

        $hub->withScope(Argument::allOf(
            Argument::type('callable')
        ))->will(function ($args) use ($scope): void {
            $args[0]($scope);
        });

        $hub->captureEvent(Argument::allOf(
            Argument::withEntry('message', 'message'),
            Argument::withEntry('level', Severity::error()),
            Argument::that(function (array $payload) {
                return $payload['message'] === 'message';
            })
        ))
            ->shouldBeCalled();

        $event = [
            'priority' => Logger::ERR,
            'message' => 'message',
            'extra' => [
                'foo' => 'bar',
            ],
        ];

        $writer->write($event);

        $this->assertSame(Logger::ERR, $scope->getExtra()['zend.priority']);
        $this->assertSame('bar', $scope->getExtra()['foo']);
    }

    public function testWriteWithTraversableExtra(): void
    {
        $hub = $this->prophesize(HubInterface::class);
        $options = new ArrayObject([
            'hub' => $hub->reveal(),
        ]);
        $writer = new Sentry($options);

        $scope = new Scope();

        $hub->withScope(Argument::allOf(
            Argument::type('callable')
        ))->will(function ($args) use ($scope): void {
            $args[0]($scope);
        });

        $hub->captureEvent(Argument::allOf(
            Argument::withEntry('message', 'message'),
            Argument::withEntry('level', Severity::error()),
            Argument::that(function (array $payload) {
                return $payload['message'] === 'message';
            })
        ))
            ->shouldBeCalled();

        $event = [
            'priority' => Logger::ERR,
            'message' => 'message',
            'extra' => new ArrayObject(['foo' => 'bar']),
        ];

        $writer->write($event);

        $this->assertSame(Logger::ERR, $scope->getExtra()['zend.priority']);
        $this->assertSame('bar', $scope->getExtra()['foo']);
    }

    public function testWriteWithInvalidExtra(): void
    {
        $hub = $this->prophesize(HubInterface::class);
        $options = new ArrayObject([
            'hub' => $hub->reveal(),
        ]);
        $writer = new Sentry($options);

        $scope = new Scope();

        $hub->withScope(Argument::allOf(
            Argument::type('callable')
        ))->will(function ($args) use ($scope): void {
            $args[0]($scope);
        });

        $hub->captureEvent(Argument::allOf(
            Argument::withEntry('message', 'message'),
            Argument::withEntry('level', Severity::error()),
            Argument::that(function (array $payload) {
                return $payload['message'] === 'message';
            })
        ))
            ->shouldBeCalled();

        $event = [
            'priority' => Logger::ERR,
            'message' => 'message',
            'extra' => false,
        ];

        $writer->write($event);
    }
}
