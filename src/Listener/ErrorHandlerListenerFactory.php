<?php

declare(strict_types=1);

namespace Facile\SentryModule\Listener;

use Psr\Container\ContainerInterface;
use Sentry\State\HubInterface;

final class ErrorHandlerListenerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandlerListener
    {
        $hub = $container->get(HubInterface::class);

        return new ErrorHandlerListener($hub);
    }
}
