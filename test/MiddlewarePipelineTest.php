<?php declare(strict_types = 1);

namespace Zumba\Test\CQRS;

use \Zumba\CQRS\MiddlewarePipeline,
	\Zumba\CQRS\Middleware,
	\Zumba\CQRS\DTO,
	\Zumba\CQRS\Response,
	\Zumba\CQRS\NullResponse;

class Counter implements Middleware {
	public static $count = 0;
	public function handle(DTO $dto, callable $next) : Response {
		static::$count++;
		return $next($dto);
	}
}

class Stop implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return new NullResponse();
	}
}

/**
 * @group cqrs
 */
class MiddlewarePipelineTest extends \Zumba\Service\Test\TestCase {
	public function testMiddlewarePipeline() {
		Counter::$count = 0;
		$pipeline = MiddlewarePipeline::fromMiddleware(
			new Counter(), new Counter(), new Counter()
		);
		$pipeline($this->getMockBuilder(DTO::class)->getMock());
		$this->assertSame(3, Counter::$count);
		Counter::$count = 0;
	}

	public function testMiddlewarePipelineWithFinal() {
		Counter::$count = 0;
		$pipeline = MiddlewarePipeline::fromMiddleware(
			new Counter(), new Counter(), new Counter()
		);
		$pipeline->append(function(DTO $dto) : Response {
			Counter::$count *= 2;
			return new NullResponse();
		});
		$pipeline($this->getMockBuilder(DTO::class)->getMock());
		$this->assertSame(6, Counter::$count);
		Counter::$count = 0;
	}

	public function testMiddlewarePipelineWithShortCircuit() {
		Counter::$count = 0;
		$pipeline = MiddlewarePipeline::fromMiddleware(
			new Counter(), new Counter(), new Stop()
		);
		$pipeline->append(function(DTO $dto) : Response {
			Counter::$count *= 2;
			return new NullResponse();
		});
		$pipeline($this->getMockBuilder(DTO::class)->getMock());
		$this->assertSame(2, Counter::$count);
		Counter::$count = 0;
	}
}
