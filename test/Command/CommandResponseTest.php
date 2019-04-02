<?php declare(strict_types = 1);

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
		$this->assertInstanceOf(Success::class, CommandResponse::fromSuccess());
	}
	public function testFail() {
		$this->assertInstanceOf(Failure::class, CommandResponse::fromThrowable(new \Exception()));
	}
}
