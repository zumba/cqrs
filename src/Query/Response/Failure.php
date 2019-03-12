<?php

namespace Zumba\CQRS\Query\Response;

class Failure extends \Zumba\CQRS\Query\QueryResponse {

	/**
	 * @var \Throwable
	 */
	protected $error;

	/**
	 * Create a new Failure response from a Throwable.
	 *
	 * Use \Zumba\CQRS\Query\QueryResponse::fail() to create this response object.
	 *
	 * @see \Zumba\CQRS\Query\QueryResponse::fail
	 */
	protected static function fromThrowable(\Throwable $error) : Failure {
		$response = new static();
		$response->error = $error;
		return $response;
	}

	/**
	 * Get the Throwable.
	 */
	public function error() : \Throwable {
		return $this->error;
	}
}
