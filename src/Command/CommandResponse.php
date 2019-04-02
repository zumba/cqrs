<?php declare(strict_types = 1);

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
	public static function fromSuccess() : CommandResponse {
		return Response\Success::make();
	}

	/**
	 * Create a Failure Response.
	 *
	 * @see \Zumba\CQRS\Response
	 */
	public static function fromThrowable(\Throwable $e) : CommandResponse {
		return Response\Failure::make($e);
	}
}
