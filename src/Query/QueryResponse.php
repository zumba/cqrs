<?php
namespace Zumba\CQRS\Query;

use \Zumba\CQRS\Response;

abstract class QueryResponse implements Response {

	/**
	 * Query Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Failed Response.
	 */
	public static function fail(\Throwable $e) : Response {
		return \Zumba\CQRS\Query\Response\Failure::fromThrowable($e);
	}
}
