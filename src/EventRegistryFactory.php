<?php declare(strict_types = 1);

namespace Zumba\CQRS;

use Zumba\Symbiosis\Event\EventRegistry;

interface EventRegistryFactory {

	/**
	 * Create a Symbiosis event registry.
	 */
	public function make() : EventRegistry;
}
