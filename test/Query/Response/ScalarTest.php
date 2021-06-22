<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Query\Response;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Query\QueryResponse;

class ScalarTest extends TestCase
{
    public function testScalar()
    {
        $response = QueryResponse::fromScalar(1000);
        $this->assertSame("1000", json_encode($response));
        $this->assertSame("1000", (string)$response);
        $this->assertSame(1000, $response->value());
    }
}
