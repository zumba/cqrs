<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Zumba\Symbiosis\Event\EventRegistry;
use Zumba\Util\Log;

final class DefaultEventRegistryFactory implements EventRegistryFactory
{

    /**
     * Create a Symbiosis event registry.
     */
    public function make(): EventRegistry
    {
        return new EventRegistry(Log::getInstance());
    }
}
