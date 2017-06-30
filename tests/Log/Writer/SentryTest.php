<?php

namespace Facile\SentryModuleTest\Log\Writer;

use ArrayObject;
use Facile\Sentry\Common\Sender\SenderInterface;
use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Log\Writer\Sentry;

class SentryTest extends \PHPUnit\Framework\TestCase
{
    public function testWithoutSenderInOptions()
    {
        $this->expectException(InvalidArgumentException::class);

        $writer = new Sentry(new ArrayObject());
    }

    public function testWrite()
    {
        $sender = $this->prophesize(SenderInterface::class);
        $options = new ArrayObject([
            'sender' => $sender->reveal(),
        ]);
        $writer = new Sentry($options);

        $sender->send(\Raven_Client::ERROR, 'message', ['foo' => 'bar'])
            ->shouldBeCalled();

        $event = [
            'priority' => \Zend\Log\Logger::ERR,
            'message' => 'message',
            'extra' => [
                'foo' => 'bar',
            ],
        ];

        $writer->write($event);
    }

    public function testWriteWithTraversableExtra()
    {
        $sender = $this->prophesize(SenderInterface::class);
        $options = [
            'sender' => $sender->reveal(),
        ];
        $writer = new Sentry($options);

        $sender->send(\Raven_Client::ERROR, 'message', ['foo' => 'bar'])
            ->shouldBeCalled();

        $event = [
            'priority' => \Zend\Log\Logger::ERR,
            'message' => 'message',
            'extra' => new ArrayObject(['foo' => 'bar']),
        ];

        $writer->write($event);
    }

    public function testWriteWithInvalidExtra()
    {
        $sender = $this->prophesize(SenderInterface::class);
        $options = [
            'sender' => $sender->reveal(),
        ];
        $writer = new Sentry($options);

        $sender->send(\Raven_Client::ERROR, 'message', [])
            ->shouldBeCalled();

        $event = [
            'priority' => \Zend\Log\Logger::ERR,
            'message' => 'message',
            'extra' => false,
        ];

        $writer->write($event);
    }
}
