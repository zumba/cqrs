<?php

namespace Zumba\CQRS\Query;

abstract class Query extends \Zumba\CQRS\DTO {

	/**
	 * Create a query DTO from an array of "key" => "value" pairs.
	 */
	public static function fromArray(array $props) : Query {
		$query = new static();
		foreach ($props as $prop => $value) {
			if (property_exists($query, $prop)) {
				$query->$prop = $value;
			}
		}
		return $query;
	}
}
