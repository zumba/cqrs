<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\CommandBus;
use Zumba\CQRS\EventRegistryFactory;
use Zumba\CQRS\InvalidHandler;
use Zumba\CQRS\MiddlewarePipeline;
use Zumba\CQRS\Provider;
use Zumba\CQRS\Test\Fixture\EventDispatchingCommandHandler;
use Zumba\CQRS\Test\Fixture\FailMiddleware;
use Zumba\CQRS\Test\Fixture\Listener;
use Zumba\CQRS\Test\Fixture\OkMiddleware;
use Zumba\Symbiosis\Event\Event;
use Zumba\Symbiosis\Event\EventRegistry;

class CommandBusTest extends TestCase
{
    public function testHandle(): void
    {
        /** @var Command&\PHPUnit\Framework\MockObject\MockObject */
        $command = $this->getMockBuilder(Command::class)->getMock();

        $handler = $this->getMockBuilder(Handler::class)
            ->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $providerNotFound = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getCommandHandler', 'getQueryHandler'])
            ->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getCommandHandler', 'getQueryHandler'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getCommandHandler')
            ->with($command)
            ->will($this->returnValue($handler));

        $provider
            ->expects($this->never())
            ->method('getQueryHandler');

        $providerNotFound
            ->expects($this->exactly(2))
            ->method('getCommandHandler')
            ->with($command)
            ->will($this->throwException(new \Zumba\CQRS\HandlerNotFound()));

        $providerNotFound
            ->expects($this->never())
            ->method('getQueryHandler');


        $bus = CommandBus::fromProviders($providerNotFound, $providerNotFound, $provider);
        $pipeline = MiddlewarePipeline::fromMiddleware(new OkMiddleware());
        $bus->withMiddleware($pipeline)->dispatch($command);
    }

    public function testHandleMiddlewareFailure(): void
    {
        /** @var Command&\PHPUnit\Framework\MockObject\MockObject */
        $command = $this->getMockBuilder(Command::class)->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getCommandHandler', 'getQueryHandler'])
            ->getMock();

        $provider
            ->expects($this->never())
            ->method('getCommandHandler');

        $provider
            ->expects($this->never())
            ->method('getQueryHandler');

        $bus = CommandBus::fromProviders($provider);
        $pipeline = MiddlewarePipeline::fromMiddleware(new OkMiddleware(), new FailMiddleware());
        $bus->withMiddleware($pipeline)->dispatch($command);
    }

    public function testDelegateNotFound(): void
    {
        /** @var Command&\PHPUnit\Framework\MockObject\MockObject */
        $dto = $this->getMockBuilder(Command::class)->getMock();
        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getCommandHandler', 'getQueryHandler'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getCommandHandler')
            ->with($dto)
            ->will($this->throwException(new \Zumba\CQRS\HandlerNotFound()));

        $provider
            ->expects($this->never())
            ->method('getQueryHandler');

        $bus = CommandBus::fromProviders($provider);
        $this->expectException(InvalidHandler::class);
        $bus->dispatch($dto);
    }

    public function testEventRegistration(): void
    {
        /** @var Command&\PHPUnit\Framework\MockObject\MockObject */
        $command = $this->getMockBuilder(Command::class)->getMock();
        $handler = new EventDispatchingCommandHandler();
        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)->getMock();

        /** @var Listener&\PHPUnit\Framework\MockObject\MockObject */
        $listener = $this->getMockBuilder(Listener::class)->onlyMethods(['listen'])->getMock();
        $listener->expects($this->once())->method('listen')
            ->with($this->callback(function (Event $event) {
                $this->assertEquals(['foo' => 'bar'], $event->data());
                return true;
            }));

        $eventRegistry = new EventRegistry();
        $eventRegistry->register('something.happened', [$listener, 'listen']);
        /** @var EventRegistryFactory&\PHPUnit\Framework\MockObject\MockObject */
        $eventRegistryFactory = $this->getMockBuilder(EventRegistryFactory::class)->getMock();
        $eventRegistryFactory->expects($this->once())->method('make')
            ->will($this->returnValue($eventRegistry));
        $provider->expects($this->once())->method('getCommandHandler')
            ->with($command)
            ->will($this->returnValue($handler));
        $bus = CommandBus::fromProviders($provider)->withEventRegistryFactory($eventRegistryFactory);
        $bus->dispatch($command);
    }
}
