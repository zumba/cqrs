<?php

namespace Zumba\Test\CQRS\Query\Response;

use \Zumba\CQRS\Query\QueryResponse;

/**
 * @group cqrs
 * @group query
 */
class ScalarTest extends \Zumba\Service\Test\TestCase {
	public function testScalar() {
		$response = QueryResponse::scalar(1000);
		$this->assertSame("1000", json_encode($response));
		$this->assertSame("1000", (string)$response);
		$this->assertSame(1000, $response->value());
	}
}
