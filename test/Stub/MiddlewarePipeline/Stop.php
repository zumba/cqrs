<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\MiddlewarePipeline;

use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\NullResponse;
use Zumba\CQRS\Response;

class Stop implements Middleware
{
    public function handle(DTO $dto, callable $next): Response
    {
        return new NullResponse();
    }
}
