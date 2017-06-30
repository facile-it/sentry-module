<?php

declare(strict_types=1);

namespace Facile\SentryModule\SendCallback;

interface CallbackInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function __invoke(array $data): array;
}
