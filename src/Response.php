<?php

namespace Zumba\CQRS;

interface Response {

	/**
	 * Create a Success Response.
	 */
	public static function ok() : Response;

	/**
	 * Create a Failed Response.
	 */
	public static function fail(\Throwable $e) : Response;
}
