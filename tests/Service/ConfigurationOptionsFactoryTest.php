<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\ConfigurationOptions;
use Facile\SentryModule\Service\ConfigurationOptionsFactory;
use Zend\ServiceManager\ServiceManager;

class ConfigurationOptionsFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'configuration' => [
                        'raven_javascript_dsn' => 'foo-dsn',
                        'raven_javascript_uri' => 'foo-uri',
                        'inject_raven_javascript' => true,
                    ],
                ],
            ],
        ];

        $serviceManager = $this->prophesize(ServiceManager::class);
        $serviceManager->get('config')->willReturn($config);

        $factory = new ConfigurationOptionsFactory();
        $options = $factory->createService($serviceManager->reveal());

        static::assertInstanceOf(ConfigurationOptions::class, $options);
        static::assertSame('foo-dsn', $options->getRavenJavascriptDsn());
        static::assertSame('foo-uri', $options->getRavenJavascriptUri());
        static::assertTrue($options->isInjectRavenJavascript());
    }
}
