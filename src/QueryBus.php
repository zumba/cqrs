<?php

namespace Zumba\CQRS;

use \Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\QueryResponse;

class QueryBus {

	/**
	 * A CQRS DTO Bus
	 *
	 * @var \Zumba\CQRS\Bus
	 */
	protected $bus;

	/**
	 * A Query Bus is a DTO bus wrapper.
	 */
	protected function __construct(Bus $bus) {
		$this->bus = $bus;
	}

	/**
	 * Create a Query Bus
	 */
	public static function fromBus(Bus $bus) : QueryBus {
		return new static($bus);
	}

	/**
	 * Pass the Query to the DTO Bus.
	 *
	 * @throws \Zumba\CQRS\InvalidResponse if handler does not return a QueryResponse
	 */
	public function dispatch(Query $query) : QueryResponse {
		$response = $this->bus->dispatch($query);
		if ($response instanceof QueryResponse) {
			return $response;
		}
		throw new \Zumba\CQRS\InvalidResponse(
			"Query Handler must return an instance of " . QueryResponse::class
		);
	}
}
