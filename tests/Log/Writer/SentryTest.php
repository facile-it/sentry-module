<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Service\Client;
use Zend\Log\Logger;
use Zend\Stdlib\ArrayObject;

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
    public function testWrite($event, $expected)
    {
        $raven = $this->prophesize(\Raven_Client::class);

        $raven->captureMessage($expected['message'], $expected['extra'], $expected['priority'])
            ->shouldBeCalledTimes(1);

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
                        ])
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
                        ]
                    ],
                ]
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
                ]
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
                ]
            ]
        ];
    }
}
