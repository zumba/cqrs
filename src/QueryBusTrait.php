<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Zumba\CQRS\Provider\ClassProvider;
use Zumba\CQRS\Provider\MethodProvider;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use Zumba\CQRS\Middleware\Logger;

trait QueryBusTrait
{
    /**
     * Create a query bus, optionally with a logger injected to a logger middleware
     */
    protected function queryBus(?LoggerInterface $logger = null): QueryBus
    {
        $bus = QueryBus::fromProviders(
            new ClassProvider(),
            new MethodProvider(),
            new SimpleDependencyProvider()
        );
        if ($logger) {
            // add logger middleware
            $logMiddleware = Logger::fromLoggerAndLevel($logger, LogLevel::INFO);
            $pipeline = MiddlewarePipeline::fromMiddleware($logMiddleware);
            $bus = $bus->withMiddleware($pipeline);
        }
        return $bus;
    }
}
