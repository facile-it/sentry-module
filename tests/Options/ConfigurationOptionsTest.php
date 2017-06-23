<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Options\ConfigurationOptions;

class ConfigurationOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testGettersAndSetters()
    {
        $optionsArray = [
            'raven_javascript_dsn' => 'foo-dsn',
            'raven_javascript_uri' => 'foo-uri',
            'raven_javascript_options' => [
                'foo' => 'bar',
            ],
            'inject_raven_javascript' => true,
        ];

        $options = new ConfigurationOptions($optionsArray);

        static::assertEquals('foo-dsn', $options->getRavenJavascriptDsn());
        static::assertEquals('foo-uri', $options->getRavenJavascriptUri());
        static::assertEquals(['foo' => 'bar'], $options->getRavenJavascriptOptions());
        static::assertTrue($options->isInjectRavenJavascript());
    }
}
