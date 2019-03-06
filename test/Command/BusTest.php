<?php

namespace Zumba\Test\CQRS\Command;

use \Zumba\CQRS\Command\Bus,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Middleware,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\Provider,
	\Zumba\CQRS\Command\Response,
	\Zumba\CQRS\Command\Success,
	\Zumba\CQRS\Command\Failure;


class OkMiddleware implements Middleware {
	public function handle(Command $command, callable $next) : Response {
		return $next($command);
	}
}

class FailMiddleware implements Middleware {
	public function handle(Command $command, callable $next) : Response {
		return Response::fail(new \Exception('failed'));
	}
}

/**
 * @group command
 */
class BusTest extends \Zumba\Service\Test\TestCase {
	public function testHandle() {
		$command = $this->getMockBuilder(Command::class)->getMock();
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
		$command = $this->getMockBuilder(Command::class)->getMock();
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
