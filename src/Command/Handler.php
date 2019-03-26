<?php

namespace Zumba\CQRS\Command;

interface Handler {

	/**
	 * Handle a Command
	 */
	public function handle(Command $command) : CommandResponse;
}
