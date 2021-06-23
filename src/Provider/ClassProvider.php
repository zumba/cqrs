<?php

declare(strict_types=1);

namespace Zumba\CQRS\Provider;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\Handler as CommandHandler;
use Zumba\CQRS\Command\HandlerFactory as CommandHandlerFactory;
use Zumba\CQRS\DTO;
use Zumba\CQRS\HandlerNotFound;
use Zumba\CQRS\InvalidHandler;
use Zumba\CQRS\Provider;
use Zumba\CQRS\Query\Handler as QueryHandler;
use Zumba\CQRS\Query\HandlerFactory as QueryHandlerFactory;
use Zumba\CQRS\Query\Query;

/**
 * ClassProvider attempts to load a HandlerFactory
 */
class ClassProvider implements Provider
{
    /**
     * Extract the factory name from the DTO and return it.
     *
     * @throws HandlerNotFound if the class does not exist.
     * @throws InvalidHandler if a handler factory class does not implement the correct interface.
     */
    protected static function getFactoryName(DTO $dto, string $factoryInterface): string
    {
        $factory = get_class($dto) . "HandlerFactory";
        if (!class_exists($factory)) {
            throw new HandlerNotFound();
        }
        if (!in_array($factoryInterface, class_implements($factory) ?: [])) {
            throw new InvalidHandler("$factory exists, but it does not implement $factoryInterface");
        }
        return $factory;
    }

    /**
     * Locate the Command handler factory and make the handler
     */
    public function getCommandHandler(Command $command): CommandHandler
    {
        $factory = static::getFactoryName($command, CommandHandlerFactory::class);
        return $factory::make();
    }

    /**
     * Locate the Query handler factory and make the handler
     */
    public function getQueryHandler(Query $query): QueryHandler
    {
        $factory = static::getFactoryName($query, QueryHandlerFactory::class);
        return $factory::make();
    }
}
