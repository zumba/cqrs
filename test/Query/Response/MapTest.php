<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Query\Response;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Query\QueryResponse;

class MapTest extends TestCase
{
    public function testMap()
    {
        $response = QueryResponse::fromMap([ 'name' => 'Goliathan' ]);
        $this->assertSame('{"name":"Goliathan"}', json_encode($response));
        $this->assertSame('{"name":"Goliathan"}', (string)$response);
        $this->assertSame('Goliathan', $response['name']);
    }
}
