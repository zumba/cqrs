<?php

namespace Zumba\CQRS\Query;

interface Handler {

	/**
	 * Handle a Query
	 */
	public function handle(Query $dto) : QueryResponse;
}
