<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Psr\EventDispatcher\EventDispatcherInterface;

interface CommandService
{

    /**
     * Get an EventDispatcherInterface
     */
    public function eventDispatcher(): EventDispatcherInterface;

    /**
     * Get a CommandBus
     */
    public function commandBus(): CommandBus;
}
