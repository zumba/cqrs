<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\HandlerNotFound;
use Zumba\CQRS\InvalidHandler;
use Zumba\CQRS\MiddlewarePipeline;
use Zumba\CQRS\Provider;
use Zumba\CQRS\Query\Handler;
use Zumba\CQRS\Query\Query;
use Zumba\CQRS\QueryBus;
use Zumba\CQRS\Test\Stub\OkMiddleware;
use Zumba\CQRS\Test\Stub\QueryBus\FailQueryMiddleware;

class QueryBusTest extends TestCase
{
    public function testHandle(): void
    {
        /** @var Query&\PHPUnit\Framework\MockObject\MockObject */
        $Query = $this->getMockBuilder(Query::class)->getMock();

        $handler = $this->getMockBuilder(Handler::class)
            ->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $providerNotFound = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getQueryHandler', 'getCommandHandler'])
            ->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getQueryHandler', 'getCommandHandler'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getQueryHandler')
            ->with($Query)
            ->will($this->returnValue($handler));

        $provider
            ->expects($this->never())
            ->method('getCommandHandler');

        $providerNotFound
            ->expects($this->exactly(2))
            ->method('getQueryHandler')
            ->with($Query)
            ->will($this->throwException(new HandlerNotFound()));

        $providerNotFound
            ->expects($this->never())
            ->method('getCommandHandler');


        $bus = QueryBus::fromProviders($providerNotFound, $providerNotFound, $provider);
        $pipeline = MiddlewarePipeline::fromMiddleware(new OkMiddleware());
        $bus->withMiddleware($pipeline)->dispatch($Query);
    }

    public function testHandleMiddlewareFailure(): void
    {
        /** @var Query&\PHPUnit\Framework\MockObject\MockObject */
        $Query = $this->getMockBuilder(Query::class)->getMock();

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getQueryHandler', 'getCommandHandler'])
            ->getMock();

        $provider
            ->expects($this->never())
            ->method('getQueryHandler');

        $provider
            ->expects($this->never())
            ->method('getCommandHandler');

        $bus = QueryBus::fromProviders($provider);
        $pipeline = MiddlewarePipeline::fromMiddleware(new OkMiddleware(), new FailQueryMiddleware());
        $bus->withMiddleware($pipeline)->dispatch($Query);
    }

    public function testDelegateNotFound(): void
    {
        /** @var Query&\PHPUnit\Framework\MockObject\MockObject */
        $dto = $this->getMockBuilder(Query::class)->getMock();
        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getQueryHandler', 'getCommandHandler'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getQueryHandler')
            ->with($dto)
            ->will($this->throwException(new \Zumba\CQRS\HandlerNotFound()));

        $provider
            ->expects($this->never())
            ->method('getCommandHandler');

        $bus = QueryBus::fromProviders($provider);
        $this->expectException(InvalidHandler::class);
        $bus->dispatch($dto);
    }
}
