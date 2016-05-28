<?php

namespace Facile\SentryModule\Service;

/**
 * Trait ClientAwareTrait.
 */
trait ClientAwareTrait
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Set the client.
     * 
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the client.
     * 
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
