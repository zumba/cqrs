<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\DTO;
use Zumba\CQRS\MiddlewarePipeline;
use Zumba\CQRS\NullResponse;
use Zumba\CQRS\Response;
use Zumba\CQRS\Test\Fixture\MiddlewarePipeline\Counter;
use Zumba\CQRS\Test\Fixture\MiddlewarePipeline\Stop;

class MiddlewarePipelineTest extends TestCase
{
    public function testMiddlewarePipeline()
    {
        Counter::$count = 0;
        $pipeline = MiddlewarePipeline::fromMiddleware(
            new Counter(),
            new Counter(),
            new Counter()
        );
        $pipeline($this->getMockBuilder(DTO::class)->getMock());
        $this->assertSame(3, Counter::$count);
        Counter::$count = 0;
    }

    public function testMiddlewarePipelineWithFinal()
    {
        Counter::$count = 0;
        $pipeline = MiddlewarePipeline::fromMiddleware(
            new Counter(),
            new Counter(),
            new Counter()
        );
        $pipeline->append(function (DTO $dto): Response {
            Counter::$count *= 2;
            return new NullResponse();
        });
        $pipeline($this->getMockBuilder(DTO::class)->getMock());
        $this->assertSame(6, Counter::$count);
        Counter::$count = 0;
    }

    public function testMiddlewarePipelineWithShortCircuit()
    {
        Counter::$count = 0;
        $pipeline = MiddlewarePipeline::fromMiddleware(
            new Counter(),
            new Counter(),
            new Stop()
        );
        $pipeline->append(function (DTO $dto): Response {
            Counter::$count *= 2;
            return new NullResponse();
        });
        $pipeline($this->getMockBuilder(DTO::class)->getMock());
        $this->assertSame(2, Counter::$count);
        Counter::$count = 0;
    }
}
