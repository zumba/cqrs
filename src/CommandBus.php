<?php

namespace Zumba\CQRS;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse,
	\Zumba\Util\Log;

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
		$handler = null;
		foreach ($this->providers as $provider) {
			try {
				return $provider->getCommandHandler($command)->handle($command);
			} catch (HandlerNotFound $e) {
				Log::write(sprintf("Handler not found by %s", get_class($provider)), Log::LEVEL_INFO, "cqrs");
			}
		}
		throw new InvalidHandler("Could not find a handler for " . get_class($command));
	}
}
