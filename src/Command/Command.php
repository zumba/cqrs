<?php

namespace Zumba\CQRS\Command;

abstract class Command extends \Zumba\CQRS\DTO {

	/**
	 * Create a command from an array of "key" => "value" pairs.
	 */
	public static function fromArray(array $props) : Command {
		$command = new static();
		foreach ($props as $prop => $value) {
			if (property_exists($command, $prop)) {
				$command->$prop = $value;
			}
		}
		return $command;
	}
}
