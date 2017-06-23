<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest\Processor;

class ToStringObject
{
    public function __toString()
    {
        return 'this is an object';
    }
}
