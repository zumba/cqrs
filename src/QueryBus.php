<?php declare(strict_types = 1);

namespace Zumba\CQRS;

use \Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\QueryResponse,
	\Zumba\Util\Log;

class QueryBus {

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
	public static function fromProviders(Provider ...$providers) : QueryBus {
		return new static(...$providers);
	}

	/**
	 * Attach middleware to the bus
	 */
	public function withMiddleware(MiddlewarePipeline $middleware) : QueryBus {
		$bus = static::fromProviders(...$this->providers);
		$bus->middleware = $middleware;
		$bus->middleware->append(function(Query $query) use ($bus) : QueryResponse {
			return $bus->delegate($query);
		});
		return $bus;
	}

	/**
	 * Pass the DTO through the middleware, if any, then delegate.
	 */
	public function dispatch(Query $query) : QueryResponse {
		$next = $this->middleware;
		if (!empty($next)) {
			return $next($query);
		}
		return $this->delegate($query);
	}

	/**
	 * Delegate the Query to a handler using the providers to build the handler.
	 *
	 * @throws InvalidHandler if a handler cannot be found.
	 */
	protected function delegate(Query $query) : QueryResponse {
		foreach ($this->providers as $provider) {
			try {
				return $provider->getQueryHandler($query)->handle($query);
			} catch (HandlerNotFound $e) {
				Log::write(sprintf("Handler not found by %s", get_class($provider)), Log::LEVEL_INFO, "cqrs");
			}
		}
		throw new InvalidHandler("Could not find a handler for " . get_class($query));
	}
}
