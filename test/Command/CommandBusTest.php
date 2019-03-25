<?php

namespace Zumba\Test\CQRS\Command;

use \Zumba\CQRS\CommandBus,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse,
	\Zumba\CQRS\Response;

/**
 * @group cqrs
 * @group command
 */
class CommandBusTest extends \Zumba\Service\Test\TestCase {
	public function testDispatch() {
		$dtoBus = $this->getMockBuilder(\Zumba\CQRS\Bus::class)->disableOriginalConstructor()->getMock();
		$response = $this->getMockBuilder(CommandResponse::class)->disableOriginalConstructor()->getMock();
		$command = $this->getMockBuilder(Command::class)->getMock();
		$dtoBus
			->expects($this->once())
			->method('dispatch')
			->with($command)
			->will($this->returnValue($response));

		$bus = CommandBus::fromBus($dtoBus);
		$this->assertSame($response, $bus->dispatch($command));
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidResponse
	 */
	public function testDispatchFailed() {
		$dtoBus = $this->getMockBuilder(\Zumba\CQRS\Bus::class)->disableOriginalConstructor()->getMock();
		$response = $this->getMockBuilder(Response::class)->getMock();
		$command = $this->getMockBuilder(Command::class)->getMock();
		$dtoBus
			->expects($this->once())
			->method('dispatch')
			->with($command)
			->will($this->returnValue($response));

		$bus = CommandBus::fromBus($dtoBus);
		$bus->dispatch($command);
	}
}
