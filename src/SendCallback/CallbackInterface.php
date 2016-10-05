<?php

namespace Facile\SentryModule\SendCallback;

interface CallbackInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function __invoke(array $data);
}
