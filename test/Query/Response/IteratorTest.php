<?php

namespace Zumba\Test\CQRS\Query\Response;

use \Zumba\CQRS\Query\QueryResponse;

/**
 * @group cqrs
 * @group query
 */
class IteratorTest extends \Zumba\Service\Test\TestCase {
	public function testIteratorList() {
		$response = QueryResponse::list([
			[ 'name' => 'The Pick of Destiny'],
			[ 'name' => 'Rize of the Fenix'],
			[ 'name' => 'Post-Apocalypto']
		]);
		$this->assertSame('[{"name":"The Pick of Destiny"},{"name":"Rize of the Fenix"},{"name":"Post-Apocalypto"}]', json_encode($response));
		$this->assertSame('[1,2,3]', (string)QueryResponse::list([1,2,3]));
		foreach ($response as $item) {
			$this->assertArrayHasKey('name', $item);
		}
		reset($response);
		$this->assertCount(3, $response);
	}

	public function testIteratorGenerator() {
		$system = function() { yield "a"; yield "b"; yield "c"; };
		$this->assertSame('["a","b","c"]', json_encode(QueryResponse::iterator($system())));
		$this->assertSame('["a","b","c"]', (string)QueryResponse::iterator($system()));

		$response = QueryResponse::iterator($system());
		foreach ($response as $item) {
			$this->assertContains($item, ["a","b","c"]);
		}
	}
}
