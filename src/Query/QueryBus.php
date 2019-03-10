<?php

namespace Zumba\CQRS\Query;

use \Zumba\CQRS\Bus;

class QueryBus {

	/**
	 * A CQRS DTO Bus
	 *
	 * @var Zumba\CQRS\Bus
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
	 */
	public function dispatch(Query $query) : QueryResponse {
		return $this->bus->dispatch($query);
	}
}
