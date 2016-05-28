<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ConfigurationOptions;
use Facile\SentryModule\Service\ConfigurationOptionsFactory;
use Zend\ServiceManager\ServiceManager;

class ConfigurationOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'configuration' => [
                        'raven_javascript_uri' => 'foo-uri',
                        'inject_raven_javascript' => true,
                        'client_for_javascript' => 'foo',
                    ],
                ],
            ],
        ];

        $serviceManager = $this->prophesize(ServiceManager::class);
        $serviceManager->get('config')->willReturn($config);

        $factory = new ConfigurationOptionsFactory();
        $options = $factory->createService($serviceManager->reveal());

        static::assertInstanceOf(ConfigurationOptions::class, $options);
        static::assertSame('foo-uri', $options->getRavenJavascriptUri());
        static::assertTrue($options->isInjectRavenJavascript());
        static::assertSame('foo', $options->getClientForJavascript());
    }
}
