<?php

namespace Zumba\CQRS\Command;

interface Middleware {

	/**
	 * Pipe a command through middleware.
	 *
	 * Receives the next middleware as an argument.  Only call $next if the middleware needs
	 * the chain to continue.
	 */
	public function handle(Command $command, callable $next) : Response;
}
