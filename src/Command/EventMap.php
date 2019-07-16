<?php declare(strict_types = 1);

namespace Zumba\CQRS\Command;

use \Zumba\CQRS\CommandBus;
use \Zumba\Util\Log;

class EventMap {

	/**
	 * @var array
	 */
	protected $map;

	/**
	 * EventMap is a mapping from events to commands
	 *
	 * @param array $map
	 */
	protected function __construct(array $map) {
		$this->map = $map;
	}

	/**
	 * Get an EventMap from a list of events.
	 *
	 * Format of the $list array:
	 * 	[
	 *     // The name of the event as keys
	 *     "event.name" => [
	 *         // the name of the command as keys
	 *         Command::class => [
	 *             // optional mapping of event body to command body.
	 *             "command body key" => "event body key",
	 *         ]
	 *     ],
	 *     // ...
	 * ]
	 */
	public static function fromEventList(array $list) : EventMap {
		foreach ($list as $event => $commands) {
			if (!is_string($event) || empty($event)) {
				throw new InvalidEventMap("Malformed event list.  Keys must be in the format 'event.name'.");
			}
			foreach ($commands as $command => $map) {
				if (!class_exists($command) || !is_a($command, Command::class)) {
					throw new InvalidEventMap("Malformed event map.  Command keys must be a command class name.");
				}
				if (!is_array($map)) {
					throw new InvalidEventMap("Malformed event map.  Value of Command keys must be an array.");
				}
			}
		}

		return new static($list);
	}

	/**
	 * Get a map of event names to callables that will handle the events and bus commands
	 *
	 * @return array
	 */
	public function mapToBus(CommandBus $bus) : array {
		$list = [];
		foreach ($this->map as $event => $commands) {
			$list[$event] = $this->getListener($bus, $commands);
		}
		return $list;
	}

	/**
	 * Get a listener for a list of commands
	 *
	 * @return callable
	 */
	protected function getListener(CommandBus $bus, array $commands) : callable {
		return function(object $event) use ($bus, $commands) : void {
			foreach ($commands as $command => $map) {
				if (in_array(WithProperties::class, class_implements($command))) {
					$instance = ((string)$command)::fromArray($this->transform($event, $map));
				} else {
					$instance = new $command();
				}
				$this->handle($instance, $bus->dispatch($instance));
			}
		};
	}

	/**
	 * Transform an event object to an array of props suitable for a command with properties.
	 */
	protected function transform(object $event, array $map) : array {
		$props = [];
		foreach ($map as $commandKey => $eventKey) {
			$props[$commandKey] = $event->$eventKey;
		}
		return $props;
	}

	/**
	 * Handle the response of a command that was dispatched.
	 */
	protected function handle(Command $command, CommandResponse $response) : void {
		if ($response instanceof Response\Failure) {
			$name = get_class($command);
			Log::getInstance()->warning(
				"Command `$name` dispatched by an event listener failed: " .
				$response->getError()->getMessage()
			);
		}
	}
}
