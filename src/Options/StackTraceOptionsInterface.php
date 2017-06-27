<?php
declare(strict_types=1);

namespace Facile\SentryModule\Options;

interface StackTraceOptionsInterface
{
    /**
     * @return string[]
     */
    public function getIgnoreBacktraceNamespaces(): array;
}
