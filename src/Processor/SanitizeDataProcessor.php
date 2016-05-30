<?php

namespace Facile\SentryModule\Processor;

/**
 * Class SanitizeDataProcessor.
 */
class SanitizeDataProcessor extends \Raven_SanitizeDataProcessor
{
    /**
     * Replace any array values with our mask if the field name or the value matches a respective regex.
     *
     * @param mixed  $item Associative array value
     * @param string $key  Associative array key
     *
     * @return string
     */
    public function sanitize(&$item, $key)
    {
        if (null === $item
            || is_scalar($item)
            || (is_object($item) && method_exists($item, '__toString'))
        ) {
            $item = is_object($item) ? (string)$item : $item;
        } elseif (is_object($item)) {
            $item = '[object '.get_class($item).']';
        } else {
            $item = '['.gettype($item).']';
        }
        parent::sanitize($item, $key);
    }
}
