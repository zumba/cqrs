<?php
namespace Zumba\CQRS\Command;

abstract class Response implements \Zumba\CQRS\Response {

	/**
	 * Command Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Success Response.
	 */
	public static function ok() : \Zumba\CQRS\Response {
		return Success::make();
	}

	/**
	 * Create a Failure Response.
	 */
	public static function fail(\Throwable $e) : \Zumba\CQRS\Response {
		return Failure::fromThrowable($e);
	}
}
