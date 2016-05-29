<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Options\ConfigurationOptions;

class ConfigurationOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersAndSetters()
    {
        $optionsArray = [
            'raven_javascript_dsn' => 'foo-dsn',
            'raven_javascript_uri' => 'foo-uri',
            'inject_raven_javascript' => true,
        ];

        $options = new ConfigurationOptions($optionsArray);

        static::assertEquals('foo-dsn', $options->getRavenJavascriptDsn());
        static::assertEquals('foo-uri', $options->getRavenJavascriptUri());
        static::assertTrue($options->isInjectRavenJavascript());
    }
}
