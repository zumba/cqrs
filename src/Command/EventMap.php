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
				if (!class_exists($command)) {
					throw new InvalidEventMap("Malformed event map.  `$command` does not exist.");
				}
				if (!is_subclass_of($command, Command::class)) {
					throw new InvalidEventMap("Malformed event map.  `$command` is not a Command class.");
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
			$list[$event] = $this->listener($bus, $commands);
		}
		return $list;
	}

	/**
	 * Get a listener for a list of commands
	 *
	 * @return callable
	 */
	protected function listener(CommandBus $bus, array $commands) : callable {
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
	 *
	 * @return array
	 */
	protected function transform(object $event, array $map) : array {
		if ($event instanceof \Zumba\Symbiosis\Framework\EventInterface) {
			$props = [];
			foreach ($map as $commandKey => $eventKey) {
				$props[$commandKey] = $this->transformValue($event->data()[$eventKey] ?? '');
			}
			return $props;
		}
		$props = [];
		foreach ($map as $commandKey => $eventKey) {
			$props[$commandKey] = $this->transformValue($event->$eventKey);
		}
		return $props;
	}

	/**
	 * Recursively transform a value to a scalar or array of scalars.
	 *
	 * @param mixed $value
	 * @return string|boolean|integer|float|array
	 */
	protected function transformValue($value) {
		if (is_array($value)) {
			return array_map(function($subValue) {
				return is_scalar($subValue) ? $subValue : (string)$subValue;
			}, $value);
		}
		return is_scalar($value) ? $value : (string)$value;
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
