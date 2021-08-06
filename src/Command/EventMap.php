<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Zumba\CQRS\Command\Response\Failure;
use Zumba\CQRS\CommandBus;

final class EventMap
{
    /**
     * @var array<string, array>
     */
    protected $map;

    /**
     * @var array<string, array>
     */
    protected $appendedData = [];

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * EventMap is a mapping from events to commands
     *
     * @param array<string, array> $map
     */
    protected function __construct(array $map)
    {
        $this->map = $map;
        $this->logger = new NullLogger();
    }

    /**
     * Get an EventMap from a list of events.
     *
     * Format of the $list array:
     *  [
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
     *
     * @param array<string, array> $list
     */
    public static function fromEventList(array $list): EventMap
    {
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
     * Attach a logger to use with the event map
     */
    public function withLogger(LoggerInterface $logger): EventMap
    {
        $map = clone $this;
        $map->logger = $logger;
        return $map;
    }

    /**
     * Attach explicit / arbitrary values to en event map
     * The listening map should know very little about the event it is listening for
     */
    public function withAppendedData(string $eventKey, string $commandKey, array $values): EventMap
    {
        $map = clone $this;
        foreach($values as $propKey => $prop) {
            $map->appendedData[$eventKey][$commandKey][$propKey] = $this->transformValue($prop);
        }
        return $map;
    }

    /**
     * Get a map of event names to callables that will handle the events and bus commands
     *
     * @return array<string, callable>
     */
    public function mapToBus(CommandBus $bus): array
    {
        $list = [];
        foreach ($this->map as $event => $commands) {
            $extraData = $this->appendedData[$event] ?? [];
            $list[$event] = $this->listener($bus, $commands, $extraData);
        }
        return $list;
    }

    /**
     * Get a listener for a list of commands
     *
     * @param array<string, array> $commands
     * @return callable
     */
    protected function listener(CommandBus $bus, array $commands, array $extraData = []): callable
    {
        return function (object $event) use ($bus, $commands, $extraData): void {
            foreach ($commands as $command => $map) {
                if (in_array(WithProperties::class, class_implements($command) ?: [])) {
                    $extraProps = $extraData[$command] ?? [];
                    $props = $this->transform($event, $map) + $extraProps;
                    $instance = ((string)$command)::fromArray($props);
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
     * @param array<string, string> $map
     * @return array<string, mixed>
     */
    protected function transform(object $event, array $map): array
    {
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
     * Transform a value to a scalar or array of scalars.
     *
     * @param mixed $value
     * @return string|boolean|integer|float|array<mixed>
     */
    protected function transformValue($value)
    {
        if (is_array($value)) {
            return array_map(function ($subValue) {
                return is_scalar($subValue) ? $subValue : (string)$subValue;
            }, $value);
        }
        return is_scalar($value) ? $value : (string)$value;
    }

    /**
     * Handle the response of a command that was dispatched.
     */
    protected function handle(Command $command, CommandResponse $response): void
    {
        if ($response instanceof Failure) {
            $name = get_class($command);
            $this->logger->warning(
                "Command `$name` dispatched by an event listener failed: " .
                $response->getError()->getMessage()
            );
        }
    }
}
