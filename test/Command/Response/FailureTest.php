<?php declare(strict_types = 1);

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
		$response = CommandResponse::fromThrowable($e);
		$this->assertSame($e, $response->getError());
	}
}
