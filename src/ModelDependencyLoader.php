<?php

namespace Zumba\CQRS;

use \Zumba\Primer\Base\Model;

/**
 * ModelDependencyLoader tries to identify and instantiate models from a class name constructor
 */
class ModelDependencyLoader {

	/**
	 * Extract model Dependencies
	 *
	 * @throws \LogicException if you're doing something wrong.
	 * @return array of zumba models
	 */
	public static function extract(string $className) : array {
		if (!class_exists($className)) {
			throw new \LogicException(
				"Undefined class: $className"
			);
		}
		$image = new \ReflectionClass($className);
		$constructor = $image->getConstructor();
		if ($constructor === null) {
			return [];
		}
		$parameters = $constructor->getParameters();
		if (empty($parameters)) {
			return [];
		}
		$dependencies = [];
		foreach ($parameters as $parameter) {
			$dependency = $parameter->getClass();
			if ($dependency === null) {
				static::fail($parameter);
			}
			if (!$dependency->isSubclassOf(Model::class)) {
				static::fail($parameter);
			}
			$dependencies[] = $dependency->newInstance();
		}
		return $dependencies;
	}

	/**
	 * Fail with a nice developer message.
	 *
	 * @throws \LogicException
	 */
	protected static function fail(\ReflectionParameter $parameter) : Response {
		throw new \LogicException(sprintf(
			"Don't be a night elf! `%s %s` is not a `%s`.",
			$parameter->getClass()->getName(),
			"$" . $parameter->getName(),
			Model::class
		));
	}
}
