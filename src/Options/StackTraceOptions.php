<?php
declare(strict_types=1);

namespace Facile\SentryModule\Options;

use Zend\Stdlib\AbstractOptions;

class StackTraceOptions extends AbstractOptions implements StackTraceOptionsInterface
{
    /**
     * @var string[]
     */
    private $ignoreBacktraceNamespaces = [];

    /**
     * @return string[]
     */
    public function getIgnoreBacktraceNamespaces(): array
    {
        return $this->ignoreBacktraceNamespaces;
    }

    /**
     * @param string[] $ignoreBacktraceNamespaces
     */
    public function setIgnoreBacktraceNamespaces(array $ignoreBacktraceNamespaces)
    {
        $this->ignoreBacktraceNamespaces = $ignoreBacktraceNamespaces;
    }
}
