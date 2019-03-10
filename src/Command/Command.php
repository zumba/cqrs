<?php

namespace Zumba\CQRS\Command;

abstract class Command extends \Zumba\CQRS\DTO {

	/**
	 * Create a command from an array of "key" => "value" pairs.
	 */
	abstract public static function fromArray(array $props) : Command;
}
