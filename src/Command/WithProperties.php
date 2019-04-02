<?php declare(strict_types = 1);

namespace Zumba\CQRS\Command;


interface WithProperties {

	/**
	 * Create a Command from an array of data.
	 */
	public static function fromArray(array $props) : Command;
}
