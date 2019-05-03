<?php declare(strict_types = 1);

namespace Zumba\Test\CQRS\Command;

use \Zumba\Primer\Test\ListenerTestCase;
use \Zumba\Primer\Model\EventQueue\Event;
use \Zumba\CQRS\Command\CommandListener;
use \Zumba\CQRS\Command\Command;
use \Zumba\CQRS\Command\WithEvent;
use \Zumba\CQRS\Command\HandlerFactory;
use \Zumba\CQRS\Command\Handler;
use \Zumba\CQRS\Command\CommandResponse;
use \Zumba\CQRS\CommandBus;

class MockListener extends CommandListener {
	protected $listenerSlug = 'mock-listener';

	protected function commandEventClassMap() : array {
		return [
			'type-a' => CorrectlyImplementedCommand::class,
			'type-b' => MissingWithPropertiesCommand::class,
		];
	}
}

class CorrectlyImplementedCommand extends Command implements WithEvent {
	protected $progress = [];
	public static function fromEvent(Event $event) : Command {
		$command = new static();
		$command->progress = $event->progress();
		return $command;
	}
}

class CorrectlyImplementedCommandHandler implements Handler, HandlerFactory {
	public static function make() : Handler {
		return new static();
	}

	public function handle(Command $command) : CommandResponse {
		return CommandResponse::fromSuccess();
	}
}

class MissingWithPropertiesCommand extends Command {
}

class MissingWithPropertiesCommandHandler implements Handler, HandlerFactory {
	public static function make() : Handler {
		return new static();
	}

	public function handle(Command $command) : CommandResponse {
		return CommandResponse::fromSuccess();
	}
}

/**
 * @group cqrs
 * @group command
 */
class CommandListenerTest extends ListenerTestCase {
	public function testCommandFromEvent() {
		$listener = $this->getMockBuilder(MockListener::class)
			->setMethods(['none'])
			->getMock();
		$event = new Event([
			'type' => 'type-a',
			'data' => json_encode(['foo' => 'bar']),
		]);
		$this->runEvent($listener, $event);
		$this->assertEquals(Event::STATUS_DONE, $event->status());
	}

	/**
	 * @expectedException \Zumba\Primer\Exception\EventQueue\SkipException
	 * @expectedExceptionMessage Event type [unknown-to-listener] not supported
	 *
	 * @return void
	 */
	public function testCommandFromEventMissingMapping() {
		$listener = $this->getMockBuilder(MockListener::class)
			->setMethods(['none'])
			->getMock();
		$event = new Event([
			'type' => 'unknown-to-listener',
			'data' => json_encode(['foo' => 'bar']),
		]);
		$this->runEvent($listener, $event);
	}

	public function testCommandFromEventIncorrectlyImplementedCommand() {
		$listener = $this->getMockBuilder(MockListener::class)
			->setMethods(['none'])
			->getMock();
		$event = new Event([
			'type' => 'type-b',
			'data' => json_encode(['foo' => 'bar']),
		]);
		$this->runEvent($listener, $event);
		$this->assertEquals(Event::STATUS_FAILED, $event->status());
		$this->assertEquals("Command must support WithEvent interface.", $event->statusMessage());
	}

	public function testPassingProgressToCommandProps() {
		$listener = $this->getMockBuilder(MockListener::class)
			->setMethods(['commandBus'])
			->getMock();
		$bus = $this->getMockBuilder(CommandBus::class)
			->disableOriginalConstructor()
			->setMethods(['dispatch'])
			->getMock();
		$listener->expects($this->once())->method('commandBus')
			->will($this->returnValue($bus));
		$bus->expects($this->once())->method('dispatch')
			->with($this->callback(function(CorrectlyImplementedCommand $command) {
				$this->assertEquals(['some' => 'progress'], $command->progress);
				return true;
			}))
			->will($this->returnValue(CommandResponse::fromSuccess()));
		$event = new Event([
			'type' => 'type-a',
			'data' => json_encode(['foo' => 'bar']),
			'progress' => json_encode(['some' => 'progress'])
		]);
		$this->runEvent($listener, $event);
		$this->assertEquals(Event::STATUS_DONE, $event->status());
	}
}
