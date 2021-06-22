<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Command\Response;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Command\CommandResponse;

class FailureTest extends TestCase
{
    public function testError()
    {
        $e = new \Exception('test');
        $response = CommandResponse::fromThrowable($e);
        $this->assertSame($e, $response->getError());
    }

    public function testMeta()
    {
        $response = CommandResponse::fromThrowable(new \Exception('test'))->withMeta(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $response->getMeta());
    }
}
