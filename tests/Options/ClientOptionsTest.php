<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Listener\ErrorHandlerListener;
use Facile\SentryModule\Options\ClientOptions;

class ClientOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $options = new ClientOptions();

        static::assertNull($options->getDsn());
        static::assertEquals([], $options->getOptions());
        static::assertEquals(ErrorHandlerListener::class, $options->getErrorHandlerListener());
        static::assertFalse($options->isRegisterExceptionHandler());
        static::assertFalse($options->isRegisterErrorHandler());
        static::assertFalse($options->isRegisterErrorListener());
        static::assertFalse($options->isRegisterShutdownFunction());
        static::assertFalse($options->isRegisterErrorListener());
    }

    public function testGettersAndSetters()
    {
        $optionsArray = [
            'dsn' => 'foo-dsn',
            'options' => ['foo' => 'bar'],
            'register_exception_handler' => true,
            'register_error_handler' => true,
            'register_shutdown_function' => true,
            'register_error_listener' => true,
            'error_handler_listener' => 'foo',
        ];

        $options = new ClientOptions($optionsArray);

        static::assertEquals('foo-dsn', $options->getDsn());
        static::assertEquals(['foo' => 'bar'], $options->getOptions());
        static::assertEquals('foo', $options->getErrorHandlerListener());
        static::assertTrue($options->isRegisterExceptionHandler());
        static::assertTrue($options->isRegisterErrorHandler());
        static::assertTrue($options->isRegisterErrorListener());
        static::assertTrue($options->isRegisterShutdownFunction());
        static::assertTrue($options->isRegisterErrorListener());
    }
}
