<?php

declare(strict_types=1);

namespace Zumba\CQRS\Provider;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\Handler as CommandHandler;
use Zumba\CQRS\DTO;
use Zumba\CQRS\HandlerNotFound;
use Zumba\CQRS\InvalidHandler;
use Zumba\CQRS\Query\Handler as QueryHandler;
use Zumba\CQRS\Query\Query;

/**
 * SimpleDependencyProvider attempts to build the handler by injecting instances into the constructor.
 */
class SimpleDependencyProvider implements \Zumba\CQRS\Provider
{
    /**
     * Extract the factory name from the DTO and return it.
     *
     * @return class-string
     * @throws HandlerNotFound if the class does not exist.
     * @throws InvalidHandler if a handler factory class does not implement the correct interface.
     */
    protected static function getHandlerName(DTO $dto, string $handlerInterface): string
    {
        $handler = get_class($dto) . "Handler";
        if (!class_exists($handler)) {
            throw new HandlerNotFound();
        }
        if (!in_array($handlerInterface, class_implements($handler) ?: [])) {
            throw new InvalidHandler("$handler exists, but it does not implement $handlerInterface");
        }
        return $handler;
    }

    /**
     * Locate the command handler factory and make the handler
     */
    public function getCommandHandler(Command $command): CommandHandler
    {
        $handler = static::getHandlerName($command, CommandHandler::class);
        $dependencies = static::extract($handler);
        return new $handler(...$dependencies);
    }

    /**
     * Locate the Query handler factory and make the handler
     */
    public function getQueryHandler(Query $query): QueryHandler
    {
        $handler = static::getHandlerName($query, QueryHandler::class);
        $dependencies = static::extract($handler);
        return new $handler(...$dependencies);
    }

    /**
     * Extract model Dependencies
     *
     * @template T
     * @param class-string<T> $className
     * @return array<int, mixed> List of class objects
     * @throws InvalidDependency if you're doing something wrong.
     */
    protected static function extract(string $className): array
    {
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
            if (!$dependency->isInstantiable()) {
                static::fail($parameter);
                return [];
            }
            $dependencyConstructor = $dependency->getConstructor();
            if (!is_null($dependencyConstructor) && !static::areAllParamsOptional($dependencyConstructor)) {
                static::fail($parameter);
                return [];
            }
            $dependencies[] = $dependency->newInstance();
        }
        return $dependencies;
    }

    /**
     * Check if all parameters in a method are optional.
     */
    protected static function areAllParamsOptional(\ReflectionMethod $method): bool
    {
        $params = $method->getParameters();
        foreach ($params as $param) {
            if (!$param->isDefaultValueAvailable()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Fail with a nice developer message.
     *
     * @throws InvalidDependency
     */
    protected static function fail(\ReflectionParameter $parameter): void
    {
        if (is_null($parameter->getClass())) {
            throw new InvalidDependency(sprintf(
                "Don't be a night elf! `%s` is not a valid class.",
                $parameter->getName()
            ));
        }
        if (!$parameter->getClass()->isInstantiable()) {
            throw new InvalidDependency(sprintf(
                "Don't be a night elf! `%s %s` cannot be instantiated.",
                $parameter->getClass()->getName(),
                "$" . $parameter->getName()
            ));
        }
        throw new InvalidDependency(sprintf(
            "Don't be a night elf! `%s %s` has required params.",
            $parameter->getClass()->getName(),
            "$" . $parameter->getName()
        ));
    }
}
