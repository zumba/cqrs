<?php

namespace Zumba\CQRS\Command;

use \Zumba\CQRS\Middleware;

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
	 * A Command Bus is a collection of middleware handlers.
	 */
	protected function __construct(Provider ...$providers) {
		$this->providers = $providers;
	}

	/**
	 * Create a Command Bus with Providers
	 */
	public static function withProviders(Provider ...$providers) : Bus {
		return new static(...$providers);
	}

	/**
	 * Attach middleware to the command bus
	 */
	public function attachMiddleware(Middleware ...$list) : void {
		$previous = empty($this->middleware) ? [$this, 'delegate'] : $this->middleware;
		while ($middleware = array_pop($list)) {
			$previous = function(Command $command) use ($middleware, $previous) : Response {
				return $middleware->handle($command, $previous);
			};
		}
		$this->middleware = $previous;
	}

	/**
	 * Pass the command through the middleware.
	 */
	public function dispatch(Command $command) : Response {
		try {
			$next = $this->middleware;
			if (!empty($next)) {
				return $next($command);
			}
			return $this->delegate($command);
		} catch(\Throwable $e) {
			return Response::fail($e);
		}
	}

	/**
	 * Delegate the command to a handler using the providers to build the handler.
	 *
	 * @throws \LogicException if a handler cannot be found.
	 */
	public function delegate(Command $command) : Response {
		$handler = null;
		foreach ($this->providers as $provider) {
			$handler = $provider->getHandler($command);
			if (!empty($handler)) {
				return $handler->handle($command);
			}
		}
		throw new \LogicException("Could not find a handler for " . get_class($command));
	}
}
