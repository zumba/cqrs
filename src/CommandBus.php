<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\CommandService;
use Zumba\CQRS\Command\EventMapperProvider;
use Zumba\CQRS\Provider\ClassProvider;
use Zumba\CQRS\Provider\MethodProvider;
use Zumba\CQRS\Provider\SimpleDependencyProvider;

final class CommandBus
{
    /**
     * A middlewarePipeline
     *
     * @var MiddlewarePipeline
     */
    protected $middleware;

    /**
     * Handler providers
     *
     * @var Provider[]
     */
    protected $providers;

    /**
     * @var \Zumba\CQRS\EventRegistryFactory
     */
    protected $eventRegistryFactory;

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * A Bus is a collection of handler providers.
     */
    protected function __construct(Provider ...$providers)
    {
        $this->providers = $providers;
        $this->eventRegistryFactory = new DefaultEventRegistryFactory();
        $this->logger = new NullLogger();
    }

    /**
     * Create a Bus with Providers
     */
    public static function fromProviders(Provider ...$providers): CommandBus
    {
        return new static(...$providers);
    }

    /**
     * Attach middleware to the bus
     */
    public function withMiddleware(MiddlewarePipeline $middleware): CommandBus
    {
        $bus = static::fromProviders(...$this->providers);
        $bus->middleware = $middleware;
        $bus->middleware->append(function (Command $command) use ($bus): CommandResponse {
            return $bus->delegate($command);
        });
        return $bus;
    }

    /**
     * Attach an EventREgistryFactory to the bus.
     */
    public function withEventRegistryFactory(EventRegistryFactory $factory): CommandBus
    {
        $bus = clone $this;
        $bus->eventRegistryFactory = $factory;
        return $bus;
    }

    /**
     * Attach a logger to use with the bus
     */
    public function withLogger(LoggerInterface $logger): CommandBus
    {
        $bus = clone $this;
        $bus->logger = $logger;
        return $bus;
    }

    /**
     * Default configured command bus.
     *
     * @return CommandBus
     */
    public static function defaultBus(): CommandBus
    {
        return static::fromProviders(
            new ClassProvider(),
            new MethodProvider(),
            new SimpleDependencyProvider()
        );
    }

    /**
     * Pass the DTO through the middleware, if any, then delegate.
     */
    public function dispatch(Command $command): CommandResponse
    {
        $next = $this->middleware;
        if (!empty($next)) {
            return $next($command);
        }
        return $this->delegate($command);
    }

    /**
     * Delegate the Command to a handler using the providers to build the handler.
     *
     * @throws InvalidHandler if a handler cannot be found.
     */
    protected function delegate(Command $command): CommandResponse
    {
        foreach ($this->providers as $provider) {
            try {
                $dispatcher = $this->eventRegistryFactory->make();
                $handler = $provider->getCommandHandler($command);
                $interfaces = class_implements($handler) ?: [];
                if (in_array(EventMapperProvider::class, $interfaces)) {
                    /** @var EventMapperProvider&\Zumba\CQRS\Command\Handler $handler */
                    $listeners = $handler->eventMapper()->eventMap()->mapToBus($this);
                    foreach ($listeners as $event => $listener) {
                        $dispatcher->register($event, $listener);
                    }
                }
                return $handler->handle($command, CommandService::make($dispatcher, $this));
            } catch (HandlerNotFound $e) {
                $this->logger->info(sprintf("Handler not found by %s", get_class($provider)));
            }
        }
        throw new InvalidHandler("Could not find a handler for " . get_class($command));
    }
}
