<?php declare(strict_types = 1);

namespace Zumba\CQRS;

use \Zumba\CQRS\Command\Command;
use \Zumba\CQRS\Command\CommandResponse;
use \Zumba\CQRS\Command\Handler;
use \Zumba\Util\Log;
use \Zumba\CQRS\Command\WithEventDispatcher;
use \Zumba\CQRS\Command\EventMapper;
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
				$handler = $provider->getCommandHandler($command);
				$interfaces = class_implements($handler);
				if (in_array(WithEventDispatcher::class, $interfaces)) {
					$handler = $this->addEventDispatcherToHandler($command, $handler);
				}
				return $handler->handle($command);
			} catch (HandlerNotFound $e) {
				Log::write(sprintf("Handler not found by %s", get_class($provider)), Log::LEVEL_INFO, "cqrs");
			}
		}
		throw new InvalidHandler("Could not find a handler for " . get_class($command));
	}

	/**
	 * Attempt to add an event dispatcher to the handler.
	 */
	protected function addEventDispatcherToHandler(Command $command, WithEventDispatcher $handler) : WithEventDispatcher {
		$mapperName = get_class($command) . "EventMapper";
		if (!class_exists($mapperName)) {
			Log::write(sprintf("`%s` implements WithEventDispatcher but no `$mapperName` class was defined.", get_class($handler)), Log::LEVEL_WARNING, "cqrs");
			return $handler;
		}
		$interfaces = class_implements($mapperName);
		if (!in_array(EventMapper::class, $interfaces)) {
			Log::write(sprintf("`$mapperName` does not implement %s", EventMapper::class), Log::LEVEL_WARNING, "cqrs");
			return $handler;
		}
		$dispatcher = new EventRegistry();
		foreach ($mapperName::eventMap()->mapToBus($this) as $event => $listener) {
			$dispatcher->register($event, $listener);
		}
		return $handler->withEventDispatcher($dispatcher);
	}
}
