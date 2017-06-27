<?php

namespace Facile\SentryModuleTest\Options;

use Facile\SentryModule\Exception\InvalidArgumentException;
use Facile\SentryModule\Options\ErrorHandlerOptions;
use OutOfBoundsException;

class ErrorHandlerOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetSkipExceptionsWithNoClassName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value in skip_exceptions');

        $options = new ErrorHandlerOptions();
        $options->setSkipExceptions([55]);
    }

    public function testSetSkipExceptionsWithNoExistingClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/values is not a class/');

        $options = new ErrorHandlerOptions();
        $options->setSkipExceptions(['foo']);
    }

    public function testGettersAndSetters()
    {
        $optionsArray = [
            'skip_exceptions' => [
                OutOfBoundsException::class,
            ],
            'error_types' => 55,
        ];

        $options = new ErrorHandlerOptions($optionsArray);

        $this->assertEquals([OutOfBoundsException::class], $options->getSkipExceptions());
        $this->assertEquals(55, $options->getErrorTypes());
    }

    public function testNullErrorTypes()
    {
        $options = new ErrorHandlerOptions();
        $options->setErrorTypes(null);

        $this->assertNull($options->getErrorTypes());
    }

    public function testInvalidErrorTypes()
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new ErrorHandlerOptions();
        $options->setErrorTypes('foo');
    }
}
