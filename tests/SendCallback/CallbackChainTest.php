<?php

namespace Facile\SentryModuleTest\SendCallback;

use Facile\SentryModule\SendCallback\CallbackChain;
use Facile\SentryModule\SendCallback\CallbackInterface;

class CallbackChainTest extends \PHPUnit\Framework\TestCase
{
    public function testChain()
    {
        $data = [
            'foo' => 'bar',
        ];

        $callback1 = $this->prophesize(CallbackInterface::class);
        $callback1->__invoke($data)->shouldBeCalled()->willReturn(['foo' => 'bar2']);
        $callback2 = $this->prophesize(CallbackInterface::class);
        $callback2->__invoke(['foo' => 'bar2'])->shouldBeCalled()->willReturn(['foo' => 'bar3']);

        $callbackChain = new CallbackChain([$callback1->reveal()]);
        $callbackChain->addCallback($callback2->reveal());
        $data = $callbackChain($data);
        $this->assertEquals(['foo' => 'bar3'], $data);
    }
}
