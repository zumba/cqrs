<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\CommandService;
use Zumba\CQRS\Command\Handler;

class NonOptionalCommandHandler implements Handler
{
    public function __construct(NonOptionalParamConstructor $notSimple)
    {
    }
    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
    }
}
