<?php declare(strict_types = 1);

namespace Zumba\CQRS\Command;

interface EventMapperProvider {

	/**
	 * Provide an EventMapper interface
	 */
	public function eventMapper() : EventMapper;
}
