<?php declare(strict_types = 1);

namespace Zumba\CQRS;

interface Middleware {

	/**
	 * Pipe a DTO through middleware.
	 *
	 * Receives the next middleware as an argument.  Only call $next if the middleware needs
	 * the chain to continue.
	 */
	public function handle(DTO $dto, callable $next) : Response;
}
