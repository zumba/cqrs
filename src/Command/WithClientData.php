<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

use Zumba\CQRS\ClientData;

interface WithClientData
{
    /**
     * Create a Command from a ClientData object
     */
    public static function fromClientData(ClientData $clientData): Command;
}
