<?php

namespace Facile\SentryModulTest\ServiceFactory;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ClientFactory;
use Facile\SentryModule\ServiceFactory\AbstractServiceFactory;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class AbstractServiceFactoryTest.
 */
class AbstractServiceFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCanCreateServiceWithName()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn(
            [
                'facile' => [
                    'sentry_factories' => [
                        'client' => ClientFactory::class,
                    ],
                    'sentry' => [
                        'client' => [
                            'default' => [],
                        ],
                    ],
                ],
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractServiceFactory();
        static::assertTrue($factory->canCreateServiceWithName($serviceLocator->reveal(), $name, $name));
    }

    public function testCanCreateServiceWithNameAndInvalidPattern()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn([]);
        $name = 'foo.sentry.client.default';
        $factory = new AbstractServiceFactory();
        static::assertFalse($factory->canCreateServiceWithName($serviceLocator->reveal(), $name, $name));
    }

    public function testCanCreateServiceWithNameAndInvalidFactory()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn(
            [
                'facile' => [
                    'sentry_factories' => [],
                    'sentry' => [
                        'client' => [],
                    ],
                ],
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractServiceFactory();
        static::assertFalse($factory->canCreateServiceWithName($serviceLocator->reveal(), $name, $name));
    }

    public function testCanCreateServiceWithNameAndInvalidService()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn(
            [
                'facile' => [
                    'sentry_factories' => [
                        'client' => ClientFactory::class,
                    ],
                    'sentry' => [
                        'client' => [],
                    ],
                ],
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractServiceFactory();
        static::assertFalse($factory->canCreateServiceWithName($serviceLocator->reveal(), $name, $name));
    }

    public function testCreateServiceWithName()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);
        $errorHandlerListener = $this->prophesize(ListenerAggregateInterface::class);
        $serviceLocator->get(ErrorHandlerListener::class)->willReturn($errorHandlerListener->reveal());

        $arrayOptions = [
            'facile' => [
                'sentry_factories' => [
                    'client' => ClientFactory::class,
                ],
                'sentry' => [
                    'client' => [
                        'default' => [
                            'dsn' => 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2',
                            'options' => [],
                        ],
                    ],
                ],
            ],
        ];

        $serviceLocator->get('config')->willReturn($arrayOptions);

        $name = 'facile.sentry.client.default';
        $asf = new AbstractServiceFactory();
        /** @var Client $service */
        $service = $asf->createServiceWithName($serviceLocator->reveal(), $name, $name);

        $this->assertInstanceOf(Client::class, $service);
        $this->assertInstanceOf(\Raven_Client::class, $service->getRaven());
        $this->assertEquals('11113333cccc', $service->getRaven()->secret_key);
        $this->assertEquals('2222226666dddd', $service->getRaven()->public_key);
        $this->assertEquals('2', $service->getRaven()->project);
        $this->assertEquals('http://sentry.yourdomain.com/api/2/store/', $service->getRaven()->server);
    }

    public function testCreateServiceWithNotExistentService()
    {
        $this->expectException(\Zend\ServiceManager\Exception\ServiceNotFoundException::class);
        $config = [
            'facile' => [
                'sentry' => [
                    'client' => [
                        'default' => [
                            'foo' => [],
                        ],
                    ],
                ],
                'sentry_factories' => [],
            ],
        ];

        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn($config);
        $asf = new AbstractServiceFactory();

        $serviceName = 'facile.sentry.client.notexists';
        $asf->createServiceWithName($serviceLocator->reveal(), $serviceName, $serviceName);
    }
}
