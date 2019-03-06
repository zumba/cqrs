<?php

namespace Zumba\Test\CQRS\Command;

use \Zumba\CQRS\Command\Response,
	\Zumba\CQRS\Command\Success,
	\Zumba\CQRS\Command\Failure;

/**
 * @group command
 */
class ResponseTest extends \Zumba\Service\Test\TestCase {
	public function testOk() {
		$this->assertInstanceOf(Success::class, Response::ok());
	}
	public function testFail() {
		$this->assertInstanceOf(Failure::class, Response::fail(new \Exception()));
	}
}
