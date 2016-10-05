<?php

namespace Facile\SentryModule\SendCallback;

final class CallbackChain implements CallbackInterface
{
    /**
     * @var array|CallbackInterface[]
     */
    protected $callbacks = [];

    /**
     * CallbackChain constructor.
     * @param array|CallbackInterface[] $callbacks
     */
    public function __construct(array $callbacks = [])
    {
        $this->callbacks = $callbacks;
    }

    /**
     * @param callable $callable
     */
    public function addCallback(callable $callable)
    {
        $this->callbacks[] = $callable;
    }

    /**
     * @param array $data
     * @return array
     */
    public function __invoke(array $data)
    {
        foreach ($this->callbacks as $callback) {
            $data = $callback($data);
        }

        return $data;
    }
}