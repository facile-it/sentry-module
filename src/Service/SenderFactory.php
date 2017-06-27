<?php
declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\Sentry\Common\Sanitizer\SanitizerInterface;
use Facile\Sentry\Common\Sender\Sender;
use Facile\Sentry\Common\Sender\SenderInterface;
use Facile\Sentry\Common\StackTrace\StackTraceInterface;
use Interop\Container\ContainerInterface;
use Raven_Client;

class SenderFactory
{
    public function __invoke(ContainerInterface $container): SenderInterface
    {
        /** @var Raven_Client $client */
        $client = $container->get(Raven_Client::class);
        /** @var SanitizerInterface $sanitizer */
        $sanitizer = $container->get(SanitizerInterface::class);
        /** @var StackTraceInterface $stackTrace */
        $stackTrace = $container->get(StackTraceInterface::class);

        return new Sender($client, $sanitizer, $stackTrace);
    }
}
