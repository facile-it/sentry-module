<?php

namespace Facile\SentryModule\Processor;

/**
 * Class SanitizeDataProcessor
 *
 * @package Facile\SentryModule\Processor
 */
class SanitizeDataProcessor extends \Raven_SanitizeDataProcessor
{
    /**
     * Replace any array values with our mask if the field name or the value matches a respective regex
     *
     * @param mixed $item       Associative array value
     * @param string $key       Associative array key
     */
    public function sanitize(&$item, $key)
    {
        if (is_object($item)) {
            $item = 'Object ' . get_class($item);
        } elseif (is_resource($item)) {
            return 'Resource ' . get_resource_type($item);
        }
        parent::sanitize($item, $key);
    }
}
