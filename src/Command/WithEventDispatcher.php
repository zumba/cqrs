<?php declare(strict_types = 1);

namespace Zumba\CQRS\Command;

use \Psr\EventDispatcher\EventDispatcherInterface;

interface WithEventDispatcher {

	/**
	 * Accept an EventDispatcherInterface and return a command handler.
	 */
	public function withEventDispatcher(EventDispatcherInterface $dispatcher) : WithEventDispatcher;
}
