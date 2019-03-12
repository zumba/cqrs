<?php
namespace Zumba\CQRS\Command;

abstract class CommandResponse implements \Zumba\CQRS\Response {

	/**
	 * Command Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Success Response.
	 */
	public static function ok() : \Zumba\CQRS\Response {
		return Response\Success::make();
	}

	/**
	 * Create a Failure Response.
	 *
	 * @see \Zumba\CQRS\Response
	 */
	public static function fail(\Throwable $e) : \Zumba\CQRS\Response {
		return Response\Failure::fromThrowable($e);
	}
}
