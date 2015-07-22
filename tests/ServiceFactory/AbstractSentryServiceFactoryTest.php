<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2015 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModulTest\ServiceFactory;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Facile\SentryModule\ServiceFactory\AbstractSentryServiceFactory;

class AbstractSentryServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateServiceWithName()
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')->getMock();

        $serviceLocatorMock->expects($this->any())->method('get')->with('Config')->willReturn(
            [
                'sentry_factories' => [
                    'raven' => 'Facile\SentryModule\Service\RavenClientFactory',
                    'ravenoptions' => 'Facile\SentryModule\Service\RavenOptionsFactory'
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
        $arrayOptions = [
            'sentry_factories' => [
                'raven' => 'Facile\SentryModule\Service\RavenClientFactory',
                'ravenoptions' => 'Facile\SentryModule\Service\RavenOptionsFactory'
            ],
            'sentry' => [
                'raven' => [
                    'default' => [
                        'dsn' => 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2',
                        'options' => []
                    ]
                ]
            ]
        ];

        $options = new RavenClientOptions($arrayOptions['sentry']['raven']['default']);

        $serviceLocatorMock->expects($this->at(0))->method('get')->with('Config')->willReturn($arrayOptions);
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('sentry.ravenoptions.default')->willReturn($options);

        $name = 'sentry.raven.default';
        $asf = new AbstractSentryServiceFactory();
        $service = $asf->createServiceWithName($serviceLocatorMock, $name, $name);

        $this->assertInstanceOf('\Raven_Client', $service);
        $this->assertEquals('11113333cccc', $service->secret_key);
        $this->assertEquals('2222226666dddd', $service->public_key);
        $this->assertEquals('2', $service->project);
        $this->assertEquals(['http://sentry.yourdomain.com/api/2/store/'], $service->servers);
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
                        'raven' => 'Facile\SentryModule\Service\RavenClientFactory',
                        'ravenoptions' => 'Facile\SentryModule\Service\RavenOptionsFactory'
                    ],
                ]
            ]
        ];
    }
}
