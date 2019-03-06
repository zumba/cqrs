<?php

namespace Zumba\CQRS\Command;

class Success extends Response {

	/**
	 * Create a new Success response.
	 *
	 * Use \Zumba\CQRS\Command\Response::ok() to create this response object.
	 *
	 * @see \Zumba\CQRS\Command\Response::ok
	 */
	protected static function make() : Success {
		return new static();
	}
}
