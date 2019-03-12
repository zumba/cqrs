<?php
namespace Zumba\CQRS\Query;

use \Zumba\CQRS\Response,
	\Zumba\CQRS\Query\Response\Scalar,
	\Zumba\CQRS\Query\Response\Map,
	\Zumba\CQRS\Query\Response\Iterator;

abstract class QueryResponse implements Response {

	/**
	 * Query Response
	 */
	protected function __construct() {
	}

	/**
	 * Create a Failed Response.
	 *
	 * @see \Zumba\CQRS\Response
	 */
	public static function fail(\Throwable $e) : Response {
		return \Zumba\CQRS\Query\Response\Failure::fromThrowable($e);
	}

	/**
	 * Get a single scalar response of key/value pairs.
	 *
	 * @param mixed $value Any scalar value (integer|float|string|boolean)
	 */
	public static function scalar($value) : Scalar {
		return Scalar::from($value);
	}

	/**
	 * Get a map of key / value pairs. (e.g. data representing an entity)
	 */
	public static function map(array $item) : Map {
		return Map::fromArray($item);
	}

	/**
	 * Get an iterator response from an array. (e.g. a list of things)
	 */
	public static function list(array $items) : Iterator {
		return Iterator::fromArray($items);
	}

	/**
	 * Get an iterator response from an Iterator. (e.g. a generator)
	 */
	public static function iterator(\Iterator $items) : Iterator {
		return Iterator::fromIterator($items);
	}
}
