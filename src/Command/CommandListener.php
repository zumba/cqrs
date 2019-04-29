<?php declare(strict_types = 1);

namespace Zumba\CQRS\Command;

use \Zumba\Base\Event\Listener;
use \Zumba\Primer\Model\EventQueue\Event;
use \Zumba\CQRS\CommandBus;
use \Zumba\CQRS\MiddlewarePipeline;
use \Zumba\CQRS\Command\Response\Failure;
use \Zumba\CQRS\Middleware\Logger;
use \Zumba\CQRS\Command\WithProperties;
use \Zumba\CQRS\Provider\ClassProvider;
use \Zumba\CQRS\Provider\MethodProvider;
use \Zumba\CQRS\Provider\ModelProvider;
use \Zumba\Primer\Exception\NotFoundException;
use \Zumba\Util\Log;

abstract class CommandListener extends Listener {

	/**
	 * @var \Zumba\CQRS\CommandBus
	 */
	protected $commandBus;

	/**
	 * Provides a key-value array with the key being event type and value being command classname.
	 */
	abstract protected function commandEventClassMap() : array;

	/**
	 * Creates a command from event data.
	 *
	 * @throws \Zumba\Primer\Exception\NotFoundException
	 * @throws \RuntimeException
	 */
	protected function commandFromEvent(Event $event) : Command {
		$commandMap = $this->commandEventClassMap();
		if (!\array_key_exists($event->type, $commandMap)) {
			throw new NotFoundException('Command class not found for given event type.');
		}
		$commandClass = $commandMap[$event->type];
		if (!in_array(WithProperties::class, class_implements($commandClass))) {
			throw new \RuntimeException('Command must support WithProperties interface.');
		}
		return $commandClass::fromArray($event->data());
	}

	/**
	 * Setup and get the command bus.
	 */
	protected function commandBus() : CommandBus {
		if (!$this->commandBus) {
			$bus = CommandBus::fromProviders(
				new ClassProvider(),
				new MethodProvider(),
				new ModelProvider()
			);
			$pipeline = MiddlewarePipeline::fromMiddleware(Logger::fromLevel(Log::LEVEL_INFO));
			$this->commandBus = $bus->withMiddleware($pipeline);
		}
		return $this->commandBus;
	}

	/**
	 * Loop thru the events
	 */
	protected function executeLoop($event) : void {
		try {
			$command = $this->commandFromEvent($event);
			$response = $this->commandBus()->dispatch($command);
			if ($response instanceof Failure) {
				throw $response->getError();
			}
			$event->done();
		} catch (NotFoundException $e) {
			$event->notSupportedByListener();
			return;
		} catch (\Exception $e) {
			$event->fail($e);
		}
	}
}
