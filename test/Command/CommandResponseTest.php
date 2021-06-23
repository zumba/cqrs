<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Command;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Response\Failure;
use Zumba\CQRS\Command\Response\Success;

class CommandResponseTest extends TestCase
{
    public function testOk(): void
    {
        $this->assertInstanceOf(Success::class, CommandResponse::fromSuccess());
    }

    public function testFail(): void
    {
        $this->assertInstanceOf(Failure::class, CommandResponse::fromThrowable(new \Exception()));
    }
}
