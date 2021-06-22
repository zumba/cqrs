<?php

declare(strict_types=1);

namespace Zumba\CQRS;

use Zumba\CQRS\Provider\ClassProvider;
use Zumba\CQRS\Provider\MethodProvider;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use Zumba\CQRS\Middleware\Logger;
use Zumba\Util\Log;

trait QueryBusTrait
{

    protected function queryBus(): QueryBus
    {
        $bus = QueryBus::fromProviders(
            new ClassProvider(),
            new MethodProvider(),
            new SimpleDependencyProvider()
        );
        $pipeline = MiddlewarePipeline::fromMiddleware(Logger::fromLevel(Log::LEVEL_INFO));
        return $bus->withMiddleware($pipeline);
    }
}
