<?php

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\InvalidHandler,
	\Zumba\CQRS\HandlerNotFound,
	\Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Query\Handler as QueryHandler,
	\Zumba\CQRS\Query\HandlerFactory as QueryHandlerFactory,
	\Zumba\CQRS\Command\HandlerFactory as CommandHandlerFactory,
	\Zumba\CQRS\Command\Handler as CommandHandler;

/**
 * ClassProvider attempts to load a HandlerFactory
 */
class ClassProvider implements \Zumba\CQRS\Provider {

	/**
	 * Extract the factory name from the DTO and return it.
	 *
	 * @throws HandlerNotFound if the class does not exist.
	 * @throws InvalidHandler if a handler factory class does not implement the correct interface.
	 */
	protected static function getFactoryName(DTO $dto, string $factoryInterface) : string {
		$factory = get_class($dto) . "HandlerFactory";
		if (!class_exists($factory)) {
			throw new \Zumba\CQRS\HandlerNotFound();
		}
		if (!in_array($factoryInterface, class_implements($factory))) {
			throw new InvalidHandler("$factory exists, but it does not implement $factoryInterface");
		}
		return $factory;
	}

	/**
	 * Locate the Command handler factory and make the handler
	 */
	public function getCommandHandler(Command $command) : CommandHandler {
		$factory = static::getFactoryName($command, CommandHandlerFactory::class);
		return $factory::make();
	}

	/**
	 * Locate the Query handler factory and make the handler
	 */
	public function getQueryHandler(Query $query) : QueryHandler {
		$factory = static::getFactoryName($query, QueryHandlerFactory::class);
		return $factory::make();
	}
}
