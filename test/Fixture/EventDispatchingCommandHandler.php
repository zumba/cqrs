<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\CommandService;
use Zumba\Symbiosis\Event\Event;

class EventDispatchingCommandHandler implements Handler
{
    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
        $commandService->eventDispatcher()->dispatch(new Event('something.happened', [
            'foo' => 'bar'
        ]));
        return CommandResponse::fromSuccess();
    }
}
