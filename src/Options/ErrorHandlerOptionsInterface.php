<?php
declare(strict_types=1);

namespace Facile\SentryModule\Options;

interface ErrorHandlerOptionsInterface
{
    /**
     * @return string[]
     */
    public function getSkipExceptions(): array;

    /**
     * @return int|null
     */
    public function getErrorTypes();
}
