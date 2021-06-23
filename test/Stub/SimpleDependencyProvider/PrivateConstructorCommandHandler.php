<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\SimpleDependencyProvider;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\CommandService;

class PrivateConstructorCommandHandler implements Handler
{
    /** @phpstan-ignore-next-line */
    public function __construct(PrivateConstructor $notSimple)
    {
    }

    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
        return CommandResponse::fromSuccess();
    }
}
