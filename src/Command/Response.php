<?php
namespace Zumba\CQRS\Command;

abstract class Response {

	/**
	 * Command Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Success Response.
	 */
	public static function ok() : Response {
		return Success::make();
	}

	/**
	 * Create a Failure Response.
	 */
	public static function fail(\Throwable $e) : Response {
		return Failure::fromThrowable($e);
	}
}
