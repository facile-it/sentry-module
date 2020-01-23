<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Log\Writer;

use ArrayObject;
use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Log\Writer\Sentry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Zend\Log\Logger;

class SentryTest extends TestCase
{
    public function testSentryWriteException(): void
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
                'exception' => new \InvalidArgumentException(),
            ],
        ];

        $writer->write($event);

        $this->assertInstanceOf(Scope::class, $scope);
        $this->assertSame(Logger::ERR, $scope->getExtra()['zend.priority']);
        $this->assertSame('bar', $scope->getExtra()['foo']);
        $this->assertArrayNotHasKey('exception', $scope->getExtra());
    }

    public function testSentryCreationException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Sentry Hub');

        new Sentry([
            'hub' => [],
        ]);
    }

    /**
     * @dataProvider eventProvider
     */
    public function testWrite(array $event, string $severity, callable $assertionsFunc): void
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
            Argument::withEntry('level', new Severity($severity)),
            Argument::that(function (array $payload) {
                return $payload['message'] === 'message';
            })
        ))
            ->shouldBeCalled();

        $writer->write($event);

        $assertionsFunc($scope);
    }

    public function eventProvider(): array
    {
        return [
            [
                [
                    'priority' => Logger::ERR,
                    'message' => 'message',
                    'extra' => [
                        'foo' => 'bar',
                    ],
                ],
                Severity::ERROR,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::ERR, $scope->getExtra()['zend.priority']);
                    $this->assertSame('bar', $scope->getExtra()['foo']);
                },
            ],
            [
                [
                    'priority' => Logger::WARN,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::WARNING,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::WARN, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
            [
                [
                    'priority' => Logger::DEBUG,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key-1' => 'extra-data-1',
                    ],
                ],
                Severity::DEBUG,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::DEBUG, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data-1', $scope->getExtra()['extra-data-key-1']);
                },
            ],
            [
                [
                    'priority' => Logger::CRIT,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::FATAL,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::CRIT, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
            [
                [
                    'priority' => Logger::ALERT,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::FATAL,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::ALERT, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
            [
                [
                    'priority' => Logger::EMERG,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::FATAL,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::EMERG, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
            [
                [
                    'priority' => Logger::NOTICE,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::INFO,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::NOTICE, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
            [
                [
                    'priority' => Logger::INFO,
                    'message' => 'message',
                    'extra' => [
                        'extra-data-key' => 'extra-data',
                    ],
                ],
                Severity::INFO,
                function (Scope $scope): void {
                    $this->assertInstanceOf(Scope::class, $scope);
                    $this->assertSame(Logger::INFO, $scope->getExtra()['zend.priority']);
                    $this->assertSame('extra-data', $scope->getExtra()['extra-data-key']);
                },
            ],
        ];
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
