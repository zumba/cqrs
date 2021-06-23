<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Provider;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Provider\ClassProvider;
use Zumba\CQRS\Command\Command;
use Zumba\CQRS\HandlerNotFound;
use Zumba\CQRS\InvalidHandler;
use Zumba\CQRS\Query\Query;
use Zumba\CQRS\Test\Fixture\SomeCommand;
use Zumba\CQRS\Test\Fixture\SomeQuery;

class ClassProviderTest extends TestCase
{
    public function testGetHandler(): void
    {
        $provider = new ClassProvider();
        /** @var Command&\PHPUnit\Framework\MockObject\MockObject */
        $command = $this->getMockBuilder(Command::class)->getMock();
        try {
            $provider->getCommandHandler($command);
        } catch (\Exception $e) {
            $this->assertInstanceOf(HandlerNotFound::class, $e);
        }
        /** @var Query&\PHPUnit\Framework\MockObject\MockObject */
        $query = $this->getMockBuilder(Query::class)->getMock();
        try {
            $provider->getQueryHandler($query);
        } catch (\Exception $e) {
            $this->assertInstanceOf(HandlerNotFound::class, $e);
        }
    }

    public function testGetHandlerThrowsIfNotImplemented(): void
    {
        $provider = new ClassProvider();
        $command = new SomeCommand();
        $this->expectException(InvalidHandler::class);
        $provider->getCommandHandler($command);
    }

    public function testGetHandlerThrowsIfNotImplementedQuery(): void
    {
        $provider = new ClassProvider();
        $command = new SomeQuery();
        $this->expectException(InvalidHandler::class);
        $provider->getQueryHandler($command);
    }
}
