<?php declare(strict_types = 1);

namespace Zumba\CQRS;

use \Zumba\CQRS\Command\Command;
use \Zumba\CQRS\Command\CommandResponse;
use \Zumba\CQRS\Command\EventMapperProvider;
use Zumba\CQRS\Provider\ClassProvider;
use Zumba\CQRS\Provider\MethodProvider;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use \Zumba\Util\Log;
use \Zumba\Symbiosis\Event\EventRegistry;

class CommandBus {

	/**
	 * A middlewarePipeline
	 *
	 * @var MiddlewarePipeline
	 */
	protected $middleware;

	/**
	 * Handler providers
	 *
	 * @var array
	 */
	protected $providers;

	/**
	 * A Bus is a collection of handler providers.
	 */
	protected function __construct(Provider ...$providers) {
		$this->providers = $providers;
	}

	/**
	 * Create a Bus with Providers
	 */
	public static function fromProviders(Provider ...$providers) : CommandBus {
		return new static(...$providers);
	}

	/**
	 * Attach middleware to the bus
	 */
	public function withMiddleware(MiddlewarePipeline $middleware) : CommandBus {
		$bus = static::fromProviders(...$this->providers);
		$bus->middleware = $middleware;
		$bus->middleware->append(function(Command $command) use ($bus) : CommandResponse {
			return $bus->delegate($command);
		});
		return $bus;
	}

	/**
	 * Default configured command bus.
	 *
	 * @return CommandBus
	 */
	public static function defaultBus() : CommandBus {
		return static::fromProviders(
			new ClassProvider(),
			new MethodProvider(),
			new SimpleDependencyProvider()
		);
	}

	/**
	 * Pass the DTO through the middleware, if any, then delegate.
	 */
	public function dispatch(Command $command) : CommandResponse {
		$next = $this->middleware;
		if (!empty($next)) {
			return $next($command);
		}
		return $this->delegate($command);
	}

	/**
	 * Delegate the Command to a handler using the providers to build the handler.
	 *
	 * @throws InvalidHandler if a handler cannot be found.
	 */
	protected function delegate(Command $command) : CommandResponse {
		foreach ($this->providers as $provider) {
			try {
				$dispatcher = new EventRegistry();
				$handler = $provider->getCommandHandler($command);
				$interfaces = class_implements($handler);
				if (in_array(EventMapperProvider::class, $interfaces)) {
					$listeners = $handler->eventMapper()->eventMap()->mapToBus($this);
					foreach ($listeners as $event => $listener) {
						$dispatcher->register($event, $listener);
					}
				}
				return $handler->handle($command, \Zumba\CQRS\Command\CommandService::make($dispatcher, $this));
			} catch (HandlerNotFound $e) {
				Log::write(sprintf("Handler not found by %s", get_class($provider)), Log::LEVEL_INFO, "cqrs");
			}
		}
		throw new InvalidHandler("Could not find a handler for " . get_class($command));
	}
}
