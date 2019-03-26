<?php

namespace Zumba\CQRS\Query;

interface HandlerProvider {

	/**
	 * Find a Query handler for the bus.
	 *
	 * It should throw an exception if it cannot find the handler.
	 *
	 * @return Handler
	 */
	public function getQueryHandler(Query $dto) : Handler;
}
