<?php
declare(strict_types=1);

namespace Facile\SentryModule\Options;

use Facile\SentryModule\Exception\InvalidArgumentException;
use Zend\Stdlib\AbstractOptions;

class ErrorHandlerOptions extends AbstractOptions implements ErrorHandlerOptionsInterface
{
    /**
     * @var string[]
     */
    private $skipExceptions = [];
    /**
     * @var int|null
     */
    private $errorTypes;

    /**
     * @return string[]
     */
    public function getSkipExceptions(): array
    {
        return $this->skipExceptions;
    }

    /**
     * @param string[] $skipExceptions
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     */
    public function setSkipExceptions(array $skipExceptions)
    {
        if (in_array(false, array_map('is_string', $skipExceptions), true)) {
            throw new InvalidArgumentException('Invalid value in skip_exceptions');
        }

        if (in_array(false, array_map('class_exists', $skipExceptions), true)) {
            throw new InvalidArgumentException('A string in skip_exceptions values is not a class');
        }

        $this->skipExceptions = $skipExceptions;
    }

    /**
     * @return int|null
     */
    public function getErrorTypes()
    {
        return $this->errorTypes;
    }

    /**
     * @param int|null $errorTypes
     * @throws \Facile\SentryModule\Exception\InvalidArgumentException
     */
    public function setErrorTypes($errorTypes)
    {
        if (null !== $errorTypes && ! is_int($errorTypes)) {
            throw new InvalidArgumentException('Invalid errorTypes value');
        }

        $this->errorTypes = $errorTypes;
    }
}
