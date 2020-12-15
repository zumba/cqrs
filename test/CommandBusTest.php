<?php declare(strict_types = 1);

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
use Zumba\CQRS\CommandService;
use Zumba\CQRS\EventRegistryFactory;
use Zumba\Symbiosis\Event\Event;
use Zumba\Symbiosis\Event\EventRegistry;

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

class EventDispatchingCommandHandler implements Handler {
	public function handle(Command $command, CommandService $commandService) : CommandResponse {
		$commandService->eventDispatcher()->dispatch(new Event('something.happened', [
			'foo' => 'bar'
		]));
		return CommandResponse::fromSuccess();
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

	public function testEventRegistration() {
		$command = $this->getMockBuilder(Command::class)->getMock();
		$handler = new EventDispatchingCommandHandler();
		$provider = $this->getMockBuilder(Provider::class)->getMock();

		$listener = $this->getMockBuilder(\stdClass::class)->setMethods(['listen'])->getMock();
		$listener->expects($this->once())->method('listen')
			->with($this->callback(function(Event $event) {
				$this->assertEquals(['foo' => 'bar'], $event->data());
				return true;
			}));

		$eventRegistry = new EventRegistry();
		$eventRegistry->register('something.happened', [$listener, 'listen']);
		$eventRegistryFactory = $this->getMockBuilder(EventRegistryFactory::class)->getMock();
		$eventRegistryFactory->expects($this->once())->method('make')
			->will($this->returnValue($eventRegistry));
		$provider->expects($this->once())->method('getCommandHandler')
			->with($command)
			->will($this->returnValue($handler));
		$bus = CommandBus::fromProviders($provider)->withEventRegistryFactory($eventRegistryFactory);
		$bus->dispatch($command);
	}
}
