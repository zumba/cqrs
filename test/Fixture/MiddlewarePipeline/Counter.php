<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\MiddlewarePipeline;

use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;

class Counter implements Middleware
{
    /**
     * Counter for number of middleware
     *
     * @var integer
     */
    public static $count = 0;

    public function handle(DTO $dto, callable $next): Response
    {
        static::$count++;
        return $next($dto);
    }
}
