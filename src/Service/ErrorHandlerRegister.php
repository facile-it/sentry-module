<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

use Zend\EventManager\EventManagerInterface;

/**
 * Class ErrorHandlerRegister.
 */
final class ErrorHandlerRegister implements ErrorHandlerRegisterInterface
{
    /**
     * @param ClientInterface                $client
     * @param EventManagerInterface $eventManager
     */
    public function registerHandlers(ClientInterface $client, EventManagerInterface $eventManager)
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
            $errorListener->attach($eventManager, $options->getErrorHandlerListenerPriority());
        }
    }
}
