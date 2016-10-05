<?php

namespace Facile\SentryModule\Transport;

interface TransportInterface
{
    /**
     * @param \Raven_Client $client
     * @param array         $data
     */
    public function __invoke(\Raven_Client $client, array $data);
}
