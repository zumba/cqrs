<?php

declare(strict_types=1);

namespace Zumba\CQRS\Provider;

use Zumba\CQRS\DTO;
use Zumba\CQRS\HandlerNotFound;
use Zumba\CQRS\Query\Query;
use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Query\Handler as QueryHandler;
use Zumba\CQRS\Query\HandlerFactory as QueryHandlerFactory;
use Zumba\CQRS\Command\HandlerFactory as CommandHandlerFactory;
use Zumba\CQRS\Command\Handler as CommandHandler;
use Zumba\CQRS\Provider;

/**
 * MethodProvider attempts to use a factory method on the handler itself.
 */
class MethodProvider implements Provider
{

    /**
     * Extract the handler name from the DTO and return it.
     *
     * @throws HandlerNotFound if the class does not exist.
     */
    protected static function getHandlerName(DTO $dto, string $factoryInterface): string
    {
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
    public function getCommandHandler(Command $command): CommandHandler
    {
        $factory = static::getHandlerName($command, CommandHandlerFactory::class);
        return $factory::make();
    }

    /**
     * Locate the Query handler factory and make the handler
     */
    public function getQueryHandler(Query $query): QueryHandler
    {
        $factory = static::getHandlerName($query, QueryHandlerFactory::class);
        return $factory::make();
    }
}
