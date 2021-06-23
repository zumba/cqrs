<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\QueryBus;

use Exception;
use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;

class FailQueryMiddleware implements Middleware
{
    public function handle(DTO $dto, callable $next): Response
    {
        return TestQueryResponse::fromThrowable(new Exception('failed'));
    }
}
