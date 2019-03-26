<?php

namespace Zumba\Test\CQRS;

use \Zumba\CQRS\CommandBus,
	\Zumba\CQRS\DTO,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Provider,
	\Zumba\CQRS\Response,
	\Zumba\CQRS\Middleware,
	\Zumba\CQRS\MiddlewarePipeline;

class TestResponse extends CommandResponse {}

class OkMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return $next($dto);
	}
}

class FailMiddleware implements Middleware {
	public function handle(DTO $dto, callable $next) : Response {
		return TestResponse::fromThrowable(new \Exception('failed'));
	}
}


/**
 * @group cqrs
 * @group command
 */
class CommandBusTest extends \Zumba\Service\Test\TestCase {
	public function testHandle() {
		$command = $this->getMockBuilder(Command::class)->getMock();
		$middle = $this->getMockBuilder(OkMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$handler = $this->getMockBuilder(Handler::class)
			->getMock();

		$providerNotFound = $this->getMockBuilder(Provider::class)
			->setMethods(['getCommandHandler', 'getQueryHandler'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getCommandHandler', 'getQueryHandler'])
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

	public function testHandleMiddlewareFailure() {
		$command = $this->getMockBuilder(Command::class)->getMock();
		$middle = $this->getMockBuilder(OkMiddleware::class)
			->setMethods(['handle'])
			->getMock();

		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getCommandHandler', 'getQueryHandler'])
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

	/**
	 * @expectedException \Zumba\CQRS\InvalidHandler
	 */
	public function testDelegateNotFound() {
		$dto = $this->getMockBuilder(Command::class)->getMock();
		$provider = $this->getMockBuilder(Provider::class)
			->setMethods(['getCommandHandler', 'getQueryHandler'])
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
		$bus->dispatch($dto);
	}
}
