<?php

namespace Zumba\CQRS\Command\Response;

class Failure extends \Zumba\CQRS\Command\CommandResponse {

	/**
	 * @var \Throwable
	 */
	protected $error;

	/**
	 * Create a new Failure response from a Throwable.
	 *
	 * Use \Zumba\CQRS\Command\Response::fail() to create this response object.
	 *
	 * @see \Zumba\CQRS\Command\Response::fail
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