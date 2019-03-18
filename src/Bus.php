<?php

namespace Zumba\CQRS;

class Bus {

	/**
	 * A middleware Closure
	 *
	 * @var callable
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
	public static function withProviders(Provider ...$providers) : Bus {
		return new static(...$providers);
	}

	/**
	 * Attach middleware to the bus
	 */
	public function attachMiddleware(Middleware ...$list) : void {
		$previous = empty($this->middleware) ? [$this, 'delegate'] : $this->middleware;
		while ($middleware = array_pop($list)) {
			$previous = function(DTO $dto) use ($middleware, $previous) : Response {
				return $middleware->handle($dto, $previous);
			};
		}
		$this->middleware = $previous;
	}

	/**
	 * Pass the DTO through the middleware.
	 */
	public function dispatch(DTO $dto) : Response {
		$next = $this->middleware;
		if (!empty($next)) {
			return $next($dto);
		}
		return $this->delegate($dto);
	}

	/**
	 * Delegate the DTO to a handler using the providers to build the handler.
	 *
	 * @throws MissingHandler if a handler cannot be found.
	 */
	public function delegate(DTO $dto) : Response {
		$handler = null;
		foreach ($this->providers as $provider) {
			$handler = $provider->getHandler($dto);
			if (!empty($handler)) {
				return $handler->handle($dto);
			}
		}
		throw new MissingHandler("Could not find a handler for " . get_class($dto));
	}
}
