<?php

declare(strict_types=1);

namespace Facile\SentryModule\Processor;

use Raven_Client;
use Raven_Processor;

/**
 * Class SanitizeDataProcessor.
 */
final class SanitizeDataProcessor extends Raven_Processor
{
    /**
     * @var Raven_Processor
     */
    protected $previous;

    /**
     * SanitizeDataProcessor constructor.
     *
     * @param Raven_Client $client
     * @param Raven_Processor|null $previous
     */
    public function __construct(Raven_Client $client, Raven_Processor $previous = null)
    {
        parent::__construct($client);

        $this->previous = $previous ?: new \Raven_Processor_SanitizeDataProcessor($client);
    }

    /**
     * @param mixed  $item Associative array value
     *
     * @return mixed
     */
    public function sanitize(&$item)
    {
        if (null === $item
            || is_scalar($item)
            || (is_object($item) && method_exists($item, '__toString'))
        ) {
            $item = is_object($item) ? '[Object: "'.addslashes((string) $item).'"]' : $item;
        } elseif (is_object($item)) {
            $item = '[object '.get_class($item).']';
        } else {
            $item = '['.gettype($item).']';
        }

        return $item;
    }

    /**
     * Process and sanitize data, modifying the existing value if necessary.
     *
     * @param array $data Array of log data
     */
    public function process(&$data)
    {
        $this->previous->process($data);

        if (! empty($data['exception'])) {
            $data['exception'] = $this->sanitizeException($data['exception']);
        }
        if (! empty($data['stacktrace'])) {
            $data['stacktrace'] = $this->sanitizeStacktrace($data['stacktrace']);
        }
        if (! empty($data['extra'])) {
            array_walk_recursive($data['extra'], [$this, 'sanitize']);
        }
    }

    public function sanitizeException(array $data): array
    {
        if (! array_key_exists('values', $data) || ! is_array($data['values'])) {
            return $data;
        }

        $values = $data['values'] ?? [];

        if (! count($values)) {
            return $data;
        }

        foreach ($values as $key => $value) {
            $values[$key] = $this->sanitizeStacktrace($value);
        }

        $data['values'] = $values;

        return $data;
    }

    public function sanitizeStacktrace(array $data): array
    {
        if (! array_key_exists('frames', $data) || ! is_array($data['frames'])) {
            return $data;
        }

        $frames = $data['frames'] ?? [];

        if (! count($frames)) {
            return $data;
        }

        foreach ($frames as $key => $frame) {
            if (! is_array($frame)) {
                continue;
            }

            if (! array_key_exists('vars', $frame) || ! is_array($frame['vars'])) {
                continue;
            }

            array_walk_recursive($frame['vars'], [$this, 'sanitize']);

            $frames[$key] = $frame;
        }

        $data['frames'] = $frames;

        return $data;
    }
}
