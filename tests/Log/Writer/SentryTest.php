<?php

namespace Facile\SentryModuleTest\Log\Writer;

use Facile\SentryModule\Log\Writer\Sentry;
use Facile\SentryModule\Service\Client;
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

    public function testWrite()
    {
        $raven = $this->prophesize(\Raven_Client::class);

        $extra = [
            'foo' => [
                'foo' => 'bar',
                'object' => 'stdClass',
                'resource' => 'stream',
            ],
        ];

        $raven->captureMessage('message', $extra, \Raven_Client::ERROR)
            ->shouldBeCalledTimes(1);

        $client = $this->prophesize(Client::class);
        $client->getRaven()->willReturn($raven->reveal());

        $options = [
            'client' => $client->reveal(),
        ];
        $writer = new Sentry($options);

        $extra2 = new \ArrayObject([
            'foo' => 'bar',
            'object' => new \stdClass(),
            'resource' => tmpfile(),
        ]);

        $event = [
            'priority' => Logger::ALERT,
            'message' => 'message',
            'extra' => ['foo' => $extra2],
        ];

        $writer->write($event);
    }
}
