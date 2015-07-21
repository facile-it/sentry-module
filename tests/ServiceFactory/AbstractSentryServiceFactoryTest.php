<?php

namespace Facile\SentryModulTest\ServiceFactory;

use Facile\SentryModule\ServiceFactory\AbstractSentryServiceFactory;

class AbstractSentryServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateServiceWithName()
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')->getMock();

        $serviceLocatorMock->expects($this->any())->method('get')->with('Config')->willReturn(
            [
                'sentry_factories' => [
                    'raven' => [
                        'factoryClass' => 'Facile\SentryModule\Service\RavenClientFactory',
                        'optionsClass' => 'Facile\SentryModule\Options\RavenClient'
                    ]
                ],
                'sentry' => [
                    'raven' => [
                        'default' => []
                    ]
                ]
            ]
        );
        $name = 'sentry.raven.default';
        $asf = new AbstractSentryServiceFactory();
        $this->assertTrue($asf->canCreateServiceWithName($serviceLocatorMock, $name, $name));
    }

    public function testCreateServiceWithName()
    {

        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')->getMock();

        $serviceLocatorMock->expects($this->any())->method('get')->with('Config')->willReturn(
            [
                'sentry_factories' => [
                    'raven' => [
                        'factoryClass' => 'Facile\SentryModule\Service\RavenClientFactory',
                        'optionsClass' => 'Facile\SentryModule\Options\RavenClient'
                    ]
                ],
                'sentry' => [
                    'raven' => [
                        'default' => []
                    ]
                ]
            ]
        );
        $name = 'sentry.raven.default';
        $asf = new AbstractSentryServiceFactory();
        $asf->createServiceWithName($serviceLocatorMock, $name, $name);
    }

    /**
     * @dataProvider invalidConfigDataProvider
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testCreateServiceWithNotExistentService($name, $config)
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')->getMock();

        $serviceLocatorMock->expects($this->any())->method('get')->with('Config')->willReturn($config);
        $asf = new AbstractSentryServiceFactory();
        $asf->createServiceWithName($serviceLocatorMock, $name, $name);
    }

    public function invalidConfigDataProvider()
    {
        return [
            [
                'sentry.raven.default',
                []
            ],
            [
                'dummy.raven.default',
                []
            ],
            [
                'sentry.raven.default',
                [
                    'sentry' => [
                        'raven' => [
                            'foo' => []
                        ]
                    ],
                    'sentry_factories' => [
                        'raven' => [
                            'factoryClass' => 'Facile\SentryModule\Service\RavenClientFactory',
                            'optionsClass' => 'Facile\SentryModule\Options\RavenClient'
                        ]
                    ],
                ]
            ]
        ];
    }
}
