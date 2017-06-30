<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Options\Configuration;
use Facile\SentryModule\Options\ErrorHandlerOptionsInterface;
use Facile\SentryModule\Options\StackTraceOptionsInterface;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGettersAndSetters()
    {
        $errorHandlerOptions = $this->prophesize(ErrorHandlerOptionsInterface::class);
        $stackTraceOptions = $this->prophesize(StackTraceOptionsInterface::class);

        $optionsArray = [
            'dsn' => 'dsn@host',
            'raven_options' => [
                'foo' => 'bar',
            ],
            'raven_javascript_dsn' => 'foo-dsn',
            'raven_javascript_uri' => 'foo-uri',
            'raven_javascript_options' => [
                'foo' => 'bar',
            ],
            'inject_raven_javascript' => true,
            'error_handler_options' => $errorHandlerOptions->reveal(),
            'stack_trace_options' => $stackTraceOptions->reveal(),
        ];

        $options = new Configuration($optionsArray);

        $this->assertEquals('dsn@host', $options->getDsn());
        $this->assertEquals(['foo' => 'bar'], $options->getRavenOptions());
        $this->assertEquals('foo-dsn', $options->getRavenJavascriptDsn());
        $this->assertEquals('foo-uri', $options->getRavenJavascriptUri());
        $this->assertEquals(['foo' => 'bar'], $options->getRavenJavascriptOptions());
        $this->assertTrue($options->shouldInjectRavenJavascript());
        $this->assertSame($errorHandlerOptions->reveal(), $options->getErrorHandlerOptions());
        $this->assertSame($stackTraceOptions->reveal(), $options->getStackTraceOptions());
    }

    public function testCreateDeps()
    {
        $options = new Configuration();
        $this->assertInstanceOf(ErrorHandlerOptionsInterface::class, $options->getErrorHandlerOptions());
        $this->assertInstanceOf(StackTraceOptionsInterface::class, $options->getStackTraceOptions());
    }
}
