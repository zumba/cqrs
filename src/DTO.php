<?php

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
}
