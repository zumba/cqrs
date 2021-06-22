<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Query\Response;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Query\QueryResponse;

class FailureTest extends TestCase
{
    public function testError()
    {
        $e = new \Exception('test');
        $response = QueryResponse::fromThrowable($e);
        $this->assertSame($e, $response->getError());
    }
}
