<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2016 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModulTest\ServiceFactory;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Options\ClientOptions as RavenClientOptions;
use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ClientFactory;
use Facile\SentryModule\ServiceFactory\AbstractClientServiceFactory;
use Interop\Container\ContainerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class AbstractClientServiceFactoryTest extends \PHPUnit_Framework_TestCase
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
                            'default' => []
                        ]
                    ]
                ]
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractClientServiceFactory();
        static::assertTrue($factory->canCreateServiceWithName($serviceLocator->reveal(), $name, $name));
    }

    public function testCanCreateServiceWithNameAndInvalidPattern()
    {
        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn([]);
        $name = 'foo.sentry.client.default';
        $factory = new AbstractClientServiceFactory();
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
                        'client' => []
                    ]
                ]
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractClientServiceFactory();
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
                        'client' => []
                    ]
                ]
            ]
        );
        $name = 'facile.sentry.client.default';
        $factory = new AbstractClientServiceFactory();
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
                            'options' => []
                        ]
                    ]
                ]
            ]
        ];

        $serviceLocator->get('config')->willReturn($arrayOptions);

        $name = 'facile.sentry.client.default';
        $asf = new AbstractClientServiceFactory();
        /** @var Client $service */
        $service = $asf->createServiceWithName($serviceLocator->reveal(), $name, $name);

        $this->assertInstanceOf(Client::class, $service);
        $this->assertInstanceOf(\Raven_Client::class, $service->getRaven());
        $this->assertEquals('11113333cccc', $service->getRaven()->secret_key);
        $this->assertEquals('2222226666dddd', $service->getRaven()->public_key);
        $this->assertEquals('2', $service->getRaven()->project);
        $this->assertEquals('http://sentry.yourdomain.com/api/2/store/', $service->getRaven()->server);
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testCreateServiceWithNotExistentService()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'client' => [
                        'default' => [
                            'foo' => []
                        ]
                    ]
                ],
            ]
        ];

        $serviceLocator = $this->prophesize(ServiceManager::class);

        $serviceLocator->get('config')->willReturn($config);
        $asf = new AbstractClientServiceFactory();

        $serviceName = 'facile.sentry.client.notexistant';
        $asf->createServiceWithName($serviceLocator->reveal(), $serviceName, $serviceName);
    }
}
