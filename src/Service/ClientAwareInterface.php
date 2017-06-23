<?php

declare(strict_types=1);

namespace Facile\SentryModule\Service;

/**
 * Interface ClientAwareInterface.
 */
interface ClientAwareInterface
{
    /**
     * Set the client.
     *
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client);

    /**
     * Get the client.
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;
}
