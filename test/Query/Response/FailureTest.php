<?php declare(strict_types = 1);

namespace Zumba\Test\CQRS\Query\Response;

use \Zumba\CQRS\Query\QueryResponse,
	\Zumba\CQRS\Query\Response\Failure;

/**
 * @group cqrs
 * @group query
 */
class FailureTest extends \Zumba\Service\Test\TestCase {
	public function testError() {
		$e = new \Exception('test');
		$response = QueryResponse::fromThrowable($e);
		$this->assertSame($e, $response->getError());
	}
}
