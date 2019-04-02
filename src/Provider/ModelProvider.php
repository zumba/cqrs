<?php declare(strict_types = 1);

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Response,
	\Zumba\CQRS\HandlerNotFound,
	\Zumba\CQRS\InvalidHandler,
	\Zumba\Primer\Base\Model,
	\Zumba\Util\Log,
	\Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Query\Handler as QueryHandler,
	\Zumba\CQRS\Command\Handler as CommandHandler;

/**
 * ModelProvider attempts to build the handler by injecting models into the constructor.
 */
class ModelProvider implements \Zumba\CQRS\Provider {

	/**
	 * Extract the factory name from the DTO and return it.
	 *
	 * @throws HandlerNotFound if the class does not exist.
	 * @throws InvalidHandler if a handler factory class does not implement the correct interface.
	 */
	protected static function getHandlerName(DTO $dto, string $handlerInterface) : string {
		$handler = get_class($dto) . "Handler";
		if (!class_exists($handler)) {
			throw new HandlerNotFound();
		}
		if (!in_array($handlerInterface, class_implements($handler))) {
			throw new InvalidHandler("$handler exists, but it does not implement $handlerInterface");
		}
		return $handler;
	}

	/**
	 * Locate the command handler factory and make the handler
	 */
	public function getCommandHandler(Command $command) : CommandHandler {
		$handler = static::getHandlerName($command, CommandHandler::class);
		$dependencies = static::extract($handler);
		return new $handler(...$dependencies);
	}

	/**
	 * Locate the Query handler factory and make the handler
	 */
	public function getQueryHandler(Query $query) : QueryHandler {
		$handler = static::getHandlerName($query, QueryHandler::class);
		$dependencies = static::extract($handler);
		return new $handler(...$dependencies);
	}

	/**
	 * Extract model Dependencies
	 *
	 * @throws InvalidDependency if you're doing something wrong.
	 * @return array of zumba models
	 */
	protected static function extract(string $className) : array {
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
				return [];
			}
			if (!$dependency->isSubclassOf(Model::class)) {
				static::fail($parameter);
				return [];
			}
			$dependencies[] = $dependency->newInstance();
		}
		return $dependencies;
	}

	/**
	 * Fail with a nice developer message.
	 *
	 * @throws InvalidDependency
	 */
	protected static function fail(\ReflectionParameter $parameter) : void {
		if (is_null($parameter->getClass())) {
			throw new InvalidDependency(sprintf(
				"Don't be a night elf! `%s` is not a `%s`.", $parameter->getName(), Model::class
			));
		}
		throw new InvalidDependency(sprintf(
			"Don't be a night elf! `%s %s` is not a `%s`.",
			$parameter->getClass()->getName(),
			"$" . $parameter->getName(),
			Model::class
		));
	}
}
