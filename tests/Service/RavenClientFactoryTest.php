<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\RavenClient as RavenClientOptions;
use Facile\SentryModule\Service\RavenClientFactory;

class RavenClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateRavenClientFromFactory()
    {
        $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();

        $arrayOptions = [
            'dsn' => 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2',
            'options' => []
        ];
        $options = new RavenClientOptions($arrayOptions);

        $serviceLocatorMock->expects($this->once())->method('get')
            ->with('sentry.ravenoptions.default')
            ->willReturn($options);
        $clientFactory = new RavenClientFactory('default');
        $service = $clientFactory->createService($serviceLocatorMock);
        $this->assertInstanceOf('\Raven_Client', $service);
        $this->assertEquals('11113333cccc', $service->secret_key);
        $this->assertEquals('2222226666dddd', $service->public_key);
        $this->assertEquals('2', $service->project);
        $this->assertEquals(['http://sentry.yourdomain.com/api/2/store/'], $service->servers);
    }
}
