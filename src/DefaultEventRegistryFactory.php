<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Psr\Log\LoggerAwareTrait;
use Zumba\Symbiosis\Event\EventRegistry;

final class DefaultEventRegistryFactory implements EventRegistryFactory
{
    use LoggerAwareTrait;

    /**
     * Create a Symbiosis event registry.
     */
    public function make(): EventRegistry
    {
        return new EventRegistry($this->logger);
    }
}
