<?php declare(strict_types = 1);

namespace Zumba\Test\CQRS\Query\Response;

use \Zumba\CQRS\Query\QueryResponse;

/**
 * @group cqrs
 * @group query
 */
class MapTest extends \Zumba\Service\Test\TestCase {
	public function testMap() {
		$response = QueryResponse::fromMap([ 'name' => 'Goliathan' ]);
		$this->assertSame('{"name":"Goliathan"}', json_encode($response));
		$this->assertSame('{"name":"Goliathan"}', (string)$response);
		$this->assertSame('Goliathan', $response['name']);
	}
}
