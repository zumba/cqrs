<?php

namespace Zumba\CQRS\Command\HandlerFactory;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\HandlerFactory,
	\Zumba\Primer\Base\Model;

/**
 * ModelInjector is a generic handler factory that tries to inject models into the handler.
 */
class ModelInjector implements \Zumba\CQRS\Command\HandlerFactory {

	/**
	 * Build a Handler
	 *
	 * Note, `$handlerName` is optional to satisfy the \Zumba\CQRS\Command\HandlerFactory
	 * interface, but not passing in a handler class name this will cause the model injector
	 * to throw a LogicException.
	 *
	 * @throws \LogicException if you're doing something wrong.
	 * @throws \RuntimeException if it cannot make the handler for some reason.
	 */
	public static function make(string $handlerName = null) : Handler {
		if (empty($handlerName)) {
			throw new \LogicException(
				"Please call ModelInjector::make with the name of the handler class to make."
			);
		}
		if (!class_exists($handlerName)) {
			throw new \LogicException(
				"Undefined handler: $handlerName"
			);
		}
		$image = new \ReflectionClass($handlerName);
		$constructor = $image->getConstructor();
		if ($constructor === null) {
			throw new \RuntimeException(
				"$handlerName::__construct is not defined."
			);
		}
		$parameters = $constructor->getParameters();
		if (empty($parameters)) {
			throw new \RuntimeException(
				"$handlerName::__construct has no parameters."
			);
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
		$handler = $image->newInstanceArgs($dependencies);
		if ($handler instanceof Handler) {
			return $handler;
		}
		throw new \LogicException(sprintf("%s does not implement %s", $handlerName, Handler::class));
	}

	/**
	 * Fail with a nice developer message.
	 *
	 * @throws \LogicException
	 */
	protected static function fail(\ReflectionParameter $parameter) : Response {
		throw new \LogicException(sprintf(
			"Don't be a night elf! `%s %s` is not a `%s`.  If you want to inject something custom, please implement `%s`",
			$parameter->getClass()->getName(),
			"$" . $parameter->getName(),
			Model::class,
			HandlerFactory::class
		));
	}
}
