<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

use Zumba\CQRS\CommandService;

interface Handler
{
    /**
     * Handle a Command
     */
    public function handle(Command $command, CommandService $commandService): CommandResponse;
}
