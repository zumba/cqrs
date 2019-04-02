<?php declare(strict_types = 1);

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\HandlerNotFound,
	\Zumba\CQRS\InvalidHandler,
	\Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Query\Handler as QueryHandler,
	\Zumba\CQRS\Query\HandlerFactory as QueryHandlerFactory,
	\Zumba\CQRS\Command\HandlerFactory as CommandHandlerFactory,
	\Zumba\CQRS\Command\Handler as CommandHandler;

/**
 * MethodProvider attempts to use a factory method on the handler itself.
 */
class MethodProvider implements \Zumba\CQRS\Provider {

	/**
	 * Extract the handler name from the DTO and return it.
	 *
	 * @throws HandlerNotFound if the class does not exist.
	 */
	protected static function getHandlerName(DTO $dto, string $factoryInterface) : string {
		$factory = get_class($dto) . "Handler";
		if (!class_exists($factory)) {
			throw new HandlerNotFound();
		}
		if (!in_array($factoryInterface, class_implements($factory))) {
			throw new HandlerNotFound();
		}
		return $factory;
	}

	/**
	 * Locate the command handler factory and make the handler
	 */
	public function getCommandHandler(Command $command) : CommandHandler {
		$factory = static::getHandlerName($command, CommandHandlerFactory::class);
		return $factory::make();
	}

	/**
	 * Locate the Query handler factory and make the handler
	 */
	public function getQueryHandler(Query $query) : QueryHandler {
		$factory = static::getHandlerName($query, QueryHandlerFactory::class);
		return $factory::make();
	}
}
