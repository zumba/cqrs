<?php

namespace Zumba\CQRS\Query\Response;

class Failure extends \Zumba\CQRS\Query\QueryResponse implements \JsonSerializable {

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
	public function getError() : \Throwable {
		return $this->error;
	}

	/**
	 * JsonSerializable implementation
	 *
	 * @see \JsonSerializable
	 * @return mixed
	 */
	public function jsonSerialize() {
		$error = $this->error->getMessage();
		return compact('error');
	}
}
