<?php

namespace Zumba\CQRS\Query;

use \Zumba\CQRS\Middleware;

class Bus {

	/**
	 * A middleware Closure
	 *
	 * @var callable
	 */
	protected $middleware;

	/**
	 * Query Handler providers
	 *
	 * @var array
	 */
	protected $providers;

	/**
	 * A Query Bus is a collection of middleware and query handler providers.
	 */
	protected function __construct(Provider ...$providers) {
		$this->providers = $providers;
	}

	/**
	 * Create a Query Bus with Providers
	 */
	public static function withProviders(Provider ...$providers) : Bus {
		return new static(...$providers);
	}

	/**
	 * Attach middleware to the Query bus
	 */
	public function attachMiddleware(Middleware ...$list) : void {
		$previous = empty($this->middleware) ? [$this, 'delegate'] : $this->middleware;
		while ($middleware = array_pop($list)) {
			$previous = function(Query $query) use ($middleware, $previous) : Response {
				return $middleware->handle($query, $previous);
			};
		}
		$this->middleware = $previous;
	}

	/**
	 * Pass the Query through the middleware.
	 */
	public function dispatch(Query $query) : Response {
		try {
			$next = $this->middleware;
			if (!empty($next)) {
				return $next($query);
			}
			return $this->delegate($query);
		} catch(\Throwable $e) {
			return Response::fail($e);
		}
	}

	/**
	 * Delegate the Query to a handler using the providers to build the handler.
	 *
	 * @throws \LogicException if a handler cannot be found.
	 */
	public function delegate(Query $query) : Response {
		$handler = null;
		foreach ($this->providers as $provider) {
			$handler = $provider->getHandler($query);
			if (!empty($handler)) {
				return $handler->handle($query);
			}
		}
		throw new \LogicException("Could not find a handler for " . get_class($query));
	}
}
