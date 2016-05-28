<?php

namespace Facile\SentryModule\Service;

interface ClientAwareInterface
{
    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client);

    /**
     * @return Client
     */
    public function getClient();
}
