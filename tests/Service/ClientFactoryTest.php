<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ClientOptions;
use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ClientAwareInterface;
use Facile\SentryModule\Service\ClientFactory;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;

class ClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInvoke()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'client' => [
                        'default' => [
                            'error_handler_listener' => 'listener',
                            'options' => [
                                'send_callback' => 'mycallbackfunc',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $errorHandlerListener = $this->prophesize(ListenerAggregateInterface::class)
            ->willImplement(ClientAwareInterface::class);
        $container = $this->prophesize(ServiceManager::class);

        $container->get('mycallbackfunc')->shouldBeCalled()
            ->willReturn(function () {
            });
        $container->get('config')->willReturn($config);
        $container->get('listener')->shouldBeCalled()->willReturn($errorHandlerListener->reveal());

        $factory = new ClientFactory('default');

        /** @var Client $service */
        $service = $factory->createService($container->reveal());

        $errorHandlerListener->getClient()->willReturn($service);

        static::assertInstanceOf(Client::class, $service);
        static::assertInstanceOf(\Raven_Client::class, $service->getRaven());
        static::assertInstanceOf(ClientOptions::class, $service->getOptions());
        static::assertInstanceOf(\Raven_ErrorHandler::class, $service->getErrorHandler());
        static::assertSame($errorHandlerListener->reveal(), $service->getErrorHandlerListener());
        static::assertSame($service, $service->getErrorHandlerListener()->getClient());
    }
}
