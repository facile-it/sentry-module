<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Raven_Client;
use Raven_ErrorHandler;
use Zend\EventManager\ListenerAggregateInterface;

interface ClientInterface
{
    /**
     * Get the Raven client.
     *
     * @return Raven_Client
     */
    public function getRaven(): Raven_Client;

    /**
     * Get the client options.
     *
     * @return ClientOptions
     */
    public function getOptions(): ClientOptions;

    /**
     * Get the Raven error handler.
     *
     * @return Raven_ErrorHandler
     */
    public function getErrorHandler(): Raven_ErrorHandler;

    /**
     * Get the error handler listener.
     *
     * @return ListenerAggregateInterface
     */
    public function getErrorHandlerListener(): ListenerAggregateInterface;
}
