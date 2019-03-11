<?php

namespace Zumba\Test\CQRS\Command;

use \Zumba\CQRS\Command\CommandResponse,
	\Zumba\CQRS\Command\Response\Success,
	\Zumba\CQRS\Command\Response\Failure;

/**
 * @group cqrs
 * @group command
 */
class CommandResponseTest extends \Zumba\Service\Test\TestCase {
	public function testOk() {
		$this->assertInstanceOf(Success::class, CommandResponse::ok());
	}
	public function testFail() {
		$this->assertInstanceOf(Failure::class, CommandResponse::fail(new \Exception()));
	}
}
