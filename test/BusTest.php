<?php

namespace Zumba\Test\CQRS;

use \Zumba\CQRS\Bus,
	\Zumba\CQRS\DTO,
	\Zumba\CQRS\Handler,
	\Zumba\CQRS\Provider,
	\Zumba\CQRS\Response,
	\Zumba\CQRS\Middleware;

class TestResponse implements Response {
	public static function fail(\Throwable $e) : Response {
		return new static();
	}
}

class OkMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return $next($dto);
	}
}

class FailMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return TestResponse::fail(new \Exception('failed'));
	}
}


/**
 * @group cqrs
 */
class BusTest extends \Zumba\Service\Test\TestCase {
	public function testHandle() {
		$command = $this->getMockBuilder(DTO::class)->getMock();
		$middle = $this->getMockBuilder(OkMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$handler = $this->getMockBuilder(Handler::class)
			->getMock();

		$providerNotFound = $this->getMockBuilder(Provider::class)
			->setMethods(['getHandler'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getHandler'])
			->getMock();

		$provider
			->expects($this->once())
			->method('getHandler')
			->with($command)
			->will($this->returnValue($handler));

		$providerNotFound
			->expects($this->exactly(2))
			->method('getHandler')
			->with($command)
			->will($this->returnValue(null));


		$bus = Bus::withProviders($providerNotFound, $providerNotFound, $provider);
		$bus->attachMiddleware(new OkMiddleware());
		$bus->dispatch($command);
	}

	public function testHandleMiddlewareFailure() {
		$command = $this->getMockBuilder(DTO::class)->getMock();
		$middle = $this->getMockBuilder(OkMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getHandler'])
			->getMock();

		$provider
			->expects($this->never())
			->method('getHandler');

		$bus = Bus::withProviders($provider);
		$bus->attachMiddleware(new OkMiddleware(), new FailMiddleware());
		$bus->dispatch($command);
	}
}
