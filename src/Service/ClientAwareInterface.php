<?php

namespace Facile\SentryModule\Service;

/**
 * Interface ClientAwareInterface.
 */
interface ClientAwareInterface
{
    /**
     * Set the client.
     *
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(Client $client);

    /**
     * Get the client.
     *
     * @return Client
     */
    public function getClient();
}
