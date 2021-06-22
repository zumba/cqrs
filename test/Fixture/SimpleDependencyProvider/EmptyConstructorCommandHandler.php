<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\CommandService;

class EmptyConstructorCommandHandler implements Handler
{
    public function __construct(EmptyConstructor $simpleDependency)
    {
    }
    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
    }
}
