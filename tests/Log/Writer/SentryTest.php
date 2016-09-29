<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Log\Writer\ContextException;
use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Service\Client;
use Prophecy\Argument;
use Zend\Log\Logger;

class SentryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorWithNoClient()
    {
        $writer = new Sentry([]);
    }

    /**
     * @dataProvider writeProvider
     */
    public function testWrite($event, $expected, $isException = false)
    {
        $raven = $this->prophesize(\Raven_Client::class);

        if (!$isException) {
            $raven->captureMessage($expected['message'], $expected['extra'], $expected['priority'],
                Argument::type('array'))
                ->shouldBeCalledTimes(1);
        } else {
            $raven->captureException(Argument::type($expected['exceptionClass']), $expected['data'])
                ->shouldBeCalledTimes(1);
        }

        $client = $this->prophesize(Client::class);
        $client->getRaven()->willReturn($raven->reveal());

        $options = [
            'client' => $client->reveal(),
        ];
        $writer = new Sentry($options);

        $writer->write($event);
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
                    'extra' => [
                        'foo' => [
                            'foo' => 'bar',
                            'object' => 'stdClass',
                            'resource' => 'stream',
                        ],
                    ],
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
                    'extra' => [
                        'foo' => 'bar',
                        'object' => 'stdClass',
                        'resource' => 'stream',
                    ],
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
                    'extra' => [],
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
                    'message' => 'test-exception',
                    'exceptionClass' => ContextException::class,
                    'data' => [
                        'extra' => [
                            'foo' => 'bar',
                        ],
                        'level' => \Raven_Client::ERROR,
                    ],
                ],
                'isException' => true,
            ],
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
                'isException' => true,
            ],
        ];
    }
}
