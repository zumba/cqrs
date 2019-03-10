<?php

namespace Zumba\CQRS;

interface Provider {

	/**
	 * Provider->getHandler will find a handler factory for the bus.
	 *
	 * It should return null if it cannot find the handler factory.
	 *
	 * @return Handler | null
	 */
	public function getHandler(DTO $dto) : ? Handler;
}
