<?php

namespace Zumba\CQRS;

interface Response {

	/**
	 * Create a Failed Response.
	 */
	public static function fromThrowable(\Throwable $e) : Response;
}
