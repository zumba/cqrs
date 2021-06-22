<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Query\Response;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Query\QueryResponse;

class IteratorTest extends TestCase
{
    public function testIteratorList()
    {
        $response = QueryResponse::fromList([
            [ 'name' => 'The Pick of Destiny'],
            [ 'name' => 'Rize of the Fenix'],
            [ 'name' => 'Post-Apocalypto']
        ]);
        $this->assertSame(
            '[{"name":"The Pick of Destiny"},{"name":"Rize of the Fenix"},{"name":"Post-Apocalypto"}]',
            json_encode($response)
        );
        $this->assertSame('[1,2,3]', (string)QueryResponse::fromList([1,2,3]));
        foreach ($response as $item) {
            $this->assertArrayHasKey('name', $item);
        }
        reset($response);
        $this->assertCount(3, $response);
    }

    public function testIteratorGenerator()
    {
        $system = function () {
            yield "a";
            yield "b";
            yield "c";
        };
        $this->assertSame('["a","b","c"]', json_encode(QueryResponse::fromIterator($system())));
        $this->assertSame('["a","b","c"]', (string)QueryResponse::fromIterator($system()));

        $response = QueryResponse::fromIterator($system());
        foreach ($response as $item) {
            $this->assertContains($item, ["a","b","c"]);
        }
        $this->assertCount(3, $response);
    }
}
