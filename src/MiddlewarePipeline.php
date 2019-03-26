<?php

namespace Zumba\CQRS;

class MiddlewarePipeline {

	/**
	 * A middleware Closure
	 *
	 * @var callable
	 */
	protected $middleware;

	/**
	 * A final callable to execute after all middleware execute.
	 *
	 * @var callable
	 */
	protected $final;

	/**
	 * Middleware Pipeline
	 */
	protected function __construct() {
		$this->middleware = function(DTO $dto) {
			return $this->finish($dto);
		};
	}

	/**
	 * Create a pipeline from one or more middleware.
	 */
	public static function fromMiddleware(Middleware ...$list) : MiddlewarePipeline {
		$pipeline = new static();
		$previous = $pipeline->middleware;
		while ($middleware = array_pop($list)) {
			$previous = function(DTO $dto) use ($middleware, $previous) : Response {
				return $middleware->handle($dto, $previous);
			};
		}
		$pipeline->middleware = $previous;
		return $pipeline;
	}

	/**
	 * This method is used to attach a final callable
	 *
	 * It will be executed after all of the middleware are finished, if all of the
	 * middleware call their $next function.
	 *
	 * @param callable $final
	 * @return void
	 */
	public function append(callable $final) : void {
		if (empty($this->final)) {
			$this->final = $final;
			return;
		}
		$previous = $this->final;
		$this->final = function(DTO $dto) use ($previous, $final) {
			return $final($previous($dto));
		};
	}

	/**
	 * This method is called after all middleware are complete.
	 *
	 * @return mixed
	 */
	protected function finish(DTO $dto) {
		$final = $this->final;
		if (is_callable($final)) {
			return $final($dto);
		}
		return null;
	}

	/**
	 * Calling the MiddlewarePipeline as a function will execute the middleware chain.
	 *
	 * @return mixed
	 */
	public function __invoke(DTO $dto) {
		$next = $this->middleware;
		return $next($dto);
	}
}
