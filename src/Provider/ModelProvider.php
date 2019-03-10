<?php

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Handler,
	\Zumba\Primer\Base\Model;

/**
 * ModelProvider attempts to build the handler by injecting models into the constructor.
 */
class ModelProvider implements \Zumba\CQRS\Provider {

	/**
	 * Build a dto handler by attempting to inject models.
	 */
	public function getHandler(DTO $dto) : ? Handler {
		try {
			$handler = get_class($dto) . "Handler";
			$dependencies = static::extract($handler);
			if (empty($dependencies)) {
				return null;
			}
			return new $handler(...$dependencies);
		} catch (\LogicException $e) {
			// if there was a logic exception, then the developer did something wrong so we should
			// bubble it so they get a nice error message.
			throw $e;
		} catch (\Throwable $e) {
			// Any other problem should just return null.  This way some other provider might
			// be able to build the handler.
			return null;
		}
	}

	/**
	 * Extract model Dependencies
	 *
	 * @throws \LogicException if you're doing something wrong.
	 * @return array of zumba models
	 */
	protected static function extract(string $className) : array {
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
