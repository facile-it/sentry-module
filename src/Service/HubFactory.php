<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Psr\Container\ContainerInterface;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;

final class HubFactory
{
    public function __invoke(ContainerInterface $container): HubInterface
    {
        $client = $container->get(ClientInterface::class);
        SentrySdk::getCurrentHub()->bindClient($client);

        return SentrySdk::getCurrentHub();
    }
}
