<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Log\Writer;

use ArrayObject;
use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Log\Writer\Sentry;
use Laminas\Log\Logger;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

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

        $reflectionClass = new ReflectionClass(Scope::class);
        $reflectionProperty = $reflectionClass->getProperty('extra');
        $reflectionProperty->setAccessible(true);
        $extra = $reflectionProperty->getValue($scope);

        $this->assertSame(Logger::ERR, $extra['laminas.priority']);
        $this->assertSame('bar', $extra['foo']);
        $this->assertArrayNotHasKey('exception', $extra);
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

    private function getScopeExtra(Scope $scope)
    {
        $reflectionClass = new ReflectionClass(Scope::class);
        $reflectionProperty = $reflectionClass->getProperty('extra');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($scope);
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
                    $this->assertSame(Logger::ERR, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('bar', $this->getScopeExtra($scope)['foo']);
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
                    $this->assertSame(Logger::WARN, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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
                    $this->assertSame(Logger::DEBUG, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data-1', $this->getScopeExtra($scope)['extra-data-key-1']);
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
                    $this->assertSame(Logger::CRIT, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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
                    $this->assertSame(Logger::ALERT, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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
                    $this->assertSame(Logger::EMERG, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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
                    $this->assertSame(Logger::NOTICE, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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
                    $this->assertSame(Logger::INFO, $this->getScopeExtra($scope)['laminas.priority']);
                    $this->assertSame('extra-data', $this->getScopeExtra($scope)['extra-data-key']);
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

        $this->assertSame(Logger::ERR, $this->getScopeExtra($scope)['laminas.priority']);
        $this->assertSame('bar', $this->getScopeExtra($scope)['foo']);
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
