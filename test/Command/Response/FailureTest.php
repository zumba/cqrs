<?php

namespace Zumba\Test\CQRS\Command\Response;

use \Zumba\CQRS\Command\CommandResponse,
	\Zumba\CQRS\Command\Response\Failure;

/**
 * @group cqrs
 * @group command
 */
class FailureTest extends \Zumba\Service\Test\TestCase {
	public function testError() {
		$e = new \Exception('test');
		$response = CommandResponse::fail($e);
		$this->assertSame($e, $response->getError());
	}
}
