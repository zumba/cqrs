<?php

namespace Zumba\CQRS\Command\Response;

class Failure extends \Zumba\CQRS\Command\CommandResponse implements \JsonSerializable {

	/**
	 * @var \Throwable
	 */
	protected $error;

	/**
	 * Create a new Failure response from a Throwable.
	 *
	 * Use \Zumba\CQRS\Command\CommandResponse::fail() to create this response object.
	 *
	 * @see \Zumba\CQRS\Command\CommandResponse::fail
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
	 * @return array
	 */
	public function jsonSerialize() {
		$error = $this->error->getMessage();
		return compact('error');
	}
}
