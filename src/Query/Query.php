<?php

namespace Zumba\CQRS\Query;

abstract class Query extends \Zumba\CQRS\DTO {

	/**
	 * Create a query DTO from an array of "key" => "value" pairs.
	 */
	abstract public static function fromArray(array $props) : Query;
}
