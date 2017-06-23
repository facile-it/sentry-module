<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Zend\EventManager\EventManagerInterface;

interface ErrorHandlerRegisterInterface
{
    /**
     * @param ClientInterface                $client
     * @param EventManagerInterface $eventManager
     */
    public function registerHandlers(ClientInterface $client, EventManagerInterface $eventManager);
}
