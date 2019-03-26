<?php

namespace Zumba\CQRS\Command;

interface HandlerProvider {

	/**
	 * Find a command handler for the bus.
	 *
	 * It should throw an exception if it cannot find the handler.
	 *
	 * @return Handler
	 */
	public function getCommandHandler(Command $dto) : Handler;
}
