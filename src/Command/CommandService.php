<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

use Zumba\CQRS\CommandBus;
use Psr\EventDispatcher\EventDispatcherInterface;

class CommandService implements \Zumba\CQRS\CommandService
{
    /**
     * An event dispatcher interface
     *
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * A command bus
     *
     * @var \Zumba\CQRS\CommandBus
     */
    protected $commandBus;

    /**
     * CommandService provides an event dispatcher and a command bus for the handlers to use.
     */
    protected function __construct(EventDispatcherInterface $eventDispatcher, CommandBus $commandBus)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->commandBus = $commandBus;
    }

    /**
     * Make a new command service.
     */
    public static function make(EventDispatcherInterface $eventDispatcher, CommandBus $commandBus): CommandService
    {
        return new static($eventDispatcher, $commandBus);
    }

    /**
     * Get an EventDispatcherInterface
     *
     * @see \Zumba\CQRS\CommandService
     */
    public function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Get a CommandBus
     *
     * @see \Zumba\CQRS\CommandService
     */
    public function commandBus(): CommandBus
    {
        return $this->commandBus;
    }
}
