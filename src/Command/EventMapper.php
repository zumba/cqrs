<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

interface EventMapper
{
    /**
     * Describe the events mapped to commands
     */
    public function eventMap(): EventMap;
}
