<?php

namespace Zumba\CQRS\Command;

use \Zumba\CQRS\Bus;

class CommandBus {

	/**
	 * A CQRS DTO Bus
	 *
	 * @var \Zumba\CQRS\Bus
	 */
	protected $bus;

	/**
	 * A Command Bus is a DTO bus wrapper.
	 */
	protected function __construct(Bus $bus) {
		$this->bus = $bus;
	}

	/**
	 * Create a Command Bus
	 */
	public static function fromBus(Bus $bus) : CommandBus {
		return new static($bus);
	}

	/**
	 * Pass the command to the DTO Bus.
	 *
	 * @throws \Zumba\CQRS\InvalidResponse if handler does not return a CommandResponse
	 */
	public function dispatch(Command $command) : CommandResponse {
		$response = $this->bus->dispatch($command);
		if ($response instanceof CommandResponse) {
			return $response;
		}
		throw new \Zumba\CQRS\InvalidResponse(
			"Command Handler must return an instance of " . CommandResponse::class
		);
	}
}
