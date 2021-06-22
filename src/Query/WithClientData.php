<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query;

use Zumba\CQRS\ClientData;

interface WithClientData
{

    /**
     * Create a Query from a ClientData object
     */
    public static function fromClientData(ClientData $clientData): Query;
}
