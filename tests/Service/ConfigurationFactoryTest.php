<?php

namespace Facile\SentryModuleTest\Service;

use Facile\SentryModule\Options\Configuration;
use Facile\SentryModule\SendCallback\CallbackChain;
use Facile\SentryModule\Service\ConfigurationFactory;
use Interop\Container\ContainerInterface;

class ConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'raven_javascript_dsn' => 'foo-dsn',
                    'raven_javascript_uri' => 'foo-uri',
                    'inject_raven_javascript' => true,
                    'error_handler_options' => [
                        'skip_exceptions' => [
                            \OutOfBoundsException::class,
                        ],
                    ],
                    'stack_trace_options' => [
                        'ignore_backtrace_namespaces' => [
                            'Foo',
                        ],
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ConfigurationFactory();
        $options = $factory($container->reveal());

        $this->assertInstanceOf(Configuration::class, $options);
        $this->assertSame('foo-dsn', $options->getRavenJavascriptDsn());
        $this->assertSame('foo-uri', $options->getRavenJavascriptUri());
        $this->assertTrue($options->shouldInjectRavenJavascript());
        $this->assertContains(
            \OutOfBoundsException::class,
            $options->getErrorHandlerOptions()->getSkipExceptions()
        );
        $this->assertContains(
            'Foo',
            $options->getStackTraceOptions()->getIgnoreBacktraceNamespaces()
        );
    }

    public function testCreateServiceWithServiceTransport()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'raven_options' => [
                        'transport' => 'transportServiceName',
                    ],
                ],
            ],
        ];

        $transport = $this->prophesize(\ArrayObject::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->get('transportServiceName')->willReturn($transport);

        $factory = new ConfigurationFactory();
        /** @var Configuration $options */
        $options = $factory($container->reveal());

        $this->assertArrayHasKey('transport', $options->getRavenOptions());
        $this->assertSame($transport->reveal(), $options->getRavenOptions()['transport']);
    }

    public function testCreateServiceWithSendCallback()
    {
        $config = [
            'facile' => [
                'sentry' => [
                    'raven_options' => [
                        'send_callback' => 'callbackServiceName',
                    ],
                ],
            ],
        ];

        $callback = function () {
        };

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->has('callbackServiceName')->willReturn(true);
        $container->get('callbackServiceName')->willReturn($callback);

        $factory = new ConfigurationFactory();
        /** @var Configuration $options */
        $options = $factory($container->reveal());

        $this->assertArrayHasKey('send_callback', $options->getRavenOptions());
        $this->assertInstanceOf(CallbackChain::class, $options->getRavenOptions()['send_callback']);
    }
}
