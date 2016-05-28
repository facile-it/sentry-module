<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Options\ConfigurationOptions;

class ConfigurationOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersAndSetters()
    {
        $optionsArray = [
            'raven_javascript_uri' => 'foo-uri',
            'inject_raven_javascript' => true,
            'client_for_javascript' => 'foo',
        ];

        $options = new ConfigurationOptions($optionsArray);

        static::assertEquals('foo-uri', $options->getRavenJavascriptUri());
        static::assertTrue($options->isInjectRavenJavascript());
        static::assertEquals('foo', $options->getClientForJavascript());
    }
}
