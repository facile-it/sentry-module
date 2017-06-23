<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Service\ClientInterface;
use Prophecy\Argument;
use Zend\Log\Logger;

class SentryTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorWithNoClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $writer = new Sentry([]);
    }

    /**
     * @dataProvider writeProvider
     */
    public function testWrite($event, $expected)
    {
        $raven = $this->prophesize(\Raven_Client::class);

        $raven->captureMessage(
            $expected['message'],
            [],
            $expected['data'],
            $expected['stack']
        )
            ->shouldBeCalledTimes(1);

        $client = $this->prophesize(ClientInterface::class);
        $client->getRaven()->willReturn($raven->reveal());

        $options = [
            'client' => $client->reveal(),
        ];
        $writer = new Sentry($options);

        $writer->write($event);
    }

    /**
     * @dataProvider writeProviderWithExceptions
     */
    public function testWriteWithException($event, $expected)
    {
        $raven = $this->prophesize(\Raven_Client::class);

        $raven->captureException(
            Argument::type($expected['exceptionClass']),
            $expected['data']
        )
            ->shouldBeCalledTimes(1);

        $client = $this->prophesize(ClientInterface::class);
        $client->getRaven()->willReturn($raven->reveal());

        $options = [
            'client' => $client->reveal(),
        ];
        $writer = new Sentry($options);

        $writer->write($event);
    }

    public function writeProviderWithExceptions()
    {
        return [
            [
                'event' => [
                    'priority' => Logger::ALERT,
                    'message' => 'same-message',
                    'extra' => [
                        'exception' => new \RuntimeException('same-message'),
                        'foo' => 'bar',
                    ],
                ],
                'expected' => [
                    'priority' => \Raven_Client::ERROR,
                    'message' => 'same-message',
                    'exceptionClass' => \RuntimeException::class,
                    'data' => [
                        'extra' => [
                            'foo' => 'bar',
                        ],
                        'level' => \Raven_Client::ERROR,
                    ],
                ],
            ],
            [
                'event' => [
                    'priority' => Logger::ALERT,
                    'message' => 'message',
                    'extra' => [
                        'exception' => new \RuntimeException('test-exception'),
                        'foo' => 'bar',
                    ],
                ],
                'expected' => [
                    'priority' => \Raven_Client::ERROR,
                    'message' => 'message',
                    'exceptionClass' => \RuntimeException::class,
                    'data' => [
                        'extra' => [
                            'foo' => 'bar',
                        ],
                        'level' => \Raven_Client::ERROR,
                        'message' => 'message :: test-exception',
                    ],
                ],
            ],
        ];
    }

    public function writeProvider()
    {
        return [
            [
                'event' => [
                    'priority' => Logger::ALERT,
                    'message' => 'message',
                    'extra' => [
                        'foo' => new \ArrayObject([
                            'foo' => 'bar',
                            'object' => new \stdClass(),
                            'resource' => tmpfile(),
                        ]),
                    ],
                ],
                'expected' => [
                    'priority' => \Raven_Client::ERROR,
                    'message' => 'message',
                    'data' => [
                        'extra' => [
                            'foo' => [
                                'foo' => 'bar',
                                'object' => 'stdClass',
                                'resource' => 'stream',
                            ],
                        ],
                        'level' => \Raven_Client::ERROR,
                    ],
                    'stack' => false,
                ],
            ],
            [
                'event' => [
                    'priority' => Logger::ALERT,
                    'message' => 'message',
                    'extra' => new \ArrayObject([
                        'foo' => 'bar',
                        'object' => new \stdClass(),
                        'resource' => tmpfile(),
                    ]),
                ],
                'expected' => [
                    'priority' => \Raven_Client::ERROR,
                    'message' => 'message',
                    'data' => [
                        'extra' => [
                            'foo' => 'bar',
                            'object' => 'stdClass',
                            'resource' => 'stream',
                        ],
                        'level' => \Raven_Client::ERROR,
                    ],
                    'stack' => false,
                ],
            ],
            [
                'event' => [
                    'priority' => Logger::ALERT,
                    'message' => 'message',
                    'extra' => null,
                ],
                'expected' => [
                    'priority' => \Raven_Client::ERROR,
                    'message' => 'message',
                    'data' => [
                        'extra' => [],
                        'level' => \Raven_Client::ERROR,
                    ],
                    'stack' => false,
                ],
            ],
        ];
    }
}
