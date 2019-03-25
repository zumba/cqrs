<?php

namespace Zumba\CQRS;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse;

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
	public function __construct(Bus $bus) {
		$this->bus = $bus;
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
