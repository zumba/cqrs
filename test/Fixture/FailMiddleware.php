<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture;

use Exception;
use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;

class FailMiddleware implements Middleware
{
    public function handle(DTO $dto, callable $next): Response
    {
        return TestResponse::fromThrowable(new Exception('failed'));
    }
}
