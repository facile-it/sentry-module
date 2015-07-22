<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Service\RavenOptionsFactory;

class RavenOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateRavenOptionsFromFactory()
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();

        $arrayOptions = [
            'sentry' => [
                'raven' => [
                    'default' => [
                        'dsn' => 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2',
                        'options' => []
                    ]
                ]
            ]
        ];

        $serviceLocatorMock->expects($this->once())->method('get')
            ->with('Config')
            ->willReturn($arrayOptions);

        $optionsFactory = new RavenOptionsFactory('default');
        $service = $optionsFactory->createService($serviceLocatorMock);
        $this->assertInstanceOf('Facile\SentryModule\Options\RavenClient', $service);
        $this->assertEquals($service->getDsn(), 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2');
        $this->assertEquals($service->getOptions(), []);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionCreatingRavenOptionsFromFactory()
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();

        $arrayOptions = [
            'sentry' => [
                'raven' => [
                ]
            ]
        ];

        $serviceLocatorMock->expects($this->once())->method('get')
            ->with('Config')
            ->willReturn($arrayOptions);

        $optionsFactory = new RavenOptionsFactory('default');
        $optionsFactory->createService($serviceLocatorMock);

    }
}
