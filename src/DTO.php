<?php declare(strict_types = 1);

namespace Zumba\CQRS;

abstract class DTO {

	/**
	 * Allow access to defined properties.
	 *
	 * @throws \OutOfBoundsException
	 * @return mixed
	 */
	final public function __get(string $name) {
		if (!property_exists($this, $name)) {
			$class = get_class($this);
			throw new \OutOfBoundsException("Undefined property $class::$name");
		}
		return $this->$name;
	}

	/**
	 * Implement __isset so that checks for empty() work as expected.
	 */
	final public function __isset(string $name) : bool {
		return !!property_exists($this, $name);
	}
}
