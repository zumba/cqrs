<?php
namespace Zumba\CQRS\Query;

abstract class Response implements \Zumba\CQRS\Response {

	/**
	 * Query Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Success Response.
	 */
	public static function ok() : \Zumba\CQRS\Response {

	}

	/**
	 * Create a Failed Response.
	 */
	public static function fail(\Throwable $e) : \Zumba\CQRS\Response {

	}
}
