<?php

namespace Zumba\Test\CQRS;

use \Zumba\CQRS\QueryBus,
	\Zumba\CQRS\DTO,
	\Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\QueryResponse,
	\Zumba\CQRS\Query\Handler,
	\Zumba\CQRS\Provider,
	\Zumba\CQRS\Response,
	\Zumba\CQRS\Middleware,
	\Zumba\CQRS\MiddlewarePipeline;

class TestQueryResponse extends QueryResponse {}

class OkQueryMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return $next($dto);
	}
}

class FailQueryMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return TestQueryResponse::fromThrowable(new \Exception('failed'));
	}
}


/**
 * @group cqrs
 * @group query
 */
class QueryBusTest extends \Zumba\Service\Test\TestCase {
	public function testHandle() {
		$Query = $this->getMockBuilder(Query::class)->getMock();
		$middle = $this->getMockBuilder(OkQueryMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$handler = $this->getMockBuilder(Handler::class)
			->getMock();

		$providerNotFound = $this->getMockBuilder(Provider::class)
			->setMethods(['getQueryHandler', 'getCommandHandler'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getQueryHandler', 'getCommandHandler'])
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
			->will($this->throwException(new \Zumba\CQRS\HandlerNotFound()));

		$providerNotFound
			->expects($this->never())
			->method('getCommandHandler');


		$bus = QueryBus::fromProviders($providerNotFound, $providerNotFound, $provider);
		$pipeline = MiddlewarePipeline::fromMiddleware(new OkQueryMiddleware());
		$bus->withMiddleware($pipeline)->dispatch($Query);
	}

	public function testHandleMiddlewareFailure() {
		$Query = $this->getMockBuilder(Query::class)->getMock();
		$middle = $this->getMockBuilder(OkQueryMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getQueryHandler', 'getCommandHandler'])
			->getMock();

		$provider
			->expects($this->never())
			->method('getQueryHandler');

		$provider
			->expects($this->never())
			->method('getCommandHandler');

		$bus = QueryBus::fromProviders($provider);
		$pipeline = MiddlewarePipeline::fromMiddleware(new OkQueryMiddleware(), new FailQueryMiddleware());
		$bus->withMiddleware($pipeline)->dispatch($Query);
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidHandler
	 */
	public function testDelegateNotFound() {
		$dto = $this->getMockBuilder(Query::class)->getMock();
		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getQueryHandler', 'getCommandHandler'])
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
		$bus->dispatch($dto);
	}
}
