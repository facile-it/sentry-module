<?php

namespace Facile\SentryModule\Service;

use Zend\EventManager\EventManagerInterface;

/**
 * Class ErrorHandlerRegister
 *
 * @package Facile\SentryModule\Service
 */
class ErrorHandlerRegister
{
    /**
     * @param Client $client
     * @param EventManagerInterface $eventManager
     */
    public function registerHandlers(Client $client, EventManagerInterface $eventManager)
    {
        $options = $client->getOptions();

        $errorHandler = $client->getErrorHandler();
        if ($options->isRegisterErrorHandler()) {
            $errorHandler->registerErrorHandler();
        }
        if ($options->isRegisterExceptionHandler()) {
            $errorHandler->registerExceptionHandler();
        }
        if ($options->isRegisterShutdownFunction()) {
            $errorHandler->registerShutdownFunction();
        }
        if ($options->isRegisterErrorListener()) {
            $errorListener = $client->getErrorHandlerListener();
            $errorListener->attach($eventManager);
        }
    }
}
