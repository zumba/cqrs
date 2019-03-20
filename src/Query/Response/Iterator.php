<?php

namespace Zumba\CQRS\Query\Response;

class Iterator extends \Zumba\CQRS\Query\QueryResponse implements \Iterator, \JsonSerializable {

	/**
	 * @var \Iterator
	 */
	protected $data;

	/**
	 * Create a new Iterator response from an array
	 *
	 * Use \Zumba\CQRS\Query\QueryResponse::list to create this response object.
	 *
	 * @see \Zumba\CQRS\Query\QueryResponse::list
	 */
	protected static function fromArray(array $data) : Iterator {
		$response = new static();
		$response->data = new \ArrayIterator($data);
		return $response;
	}

	/**
	 * Create a new Iterator response from an \Iterator (e.g. a Generator)
	 *
	 * Use \Zumba\CQRS\Query\QueryResponse::iterator to create this response object.
	 *
	 * @see \Zumba\CQRS\Query\QueryResponse::iterator
	 */
	protected static function fromIterator(\Iterator $data) : Iterator {
		$response = new static();
		$response->data = $data;
		return $response;
	}

	/**
	 * JsonSerializable implementation
	 *
	 * @see \JsonSerializable
	 * @return array
	 */
	public function jsonSerialize() {
		return iterator_to_array($this->data);
	}

	public function __toString() : string {
		if ($this->data->valid()) {
			return json_encode($this->jsonSerialize()) ?: '';
		}
		return "Invalid Iterator.";
	}

	/**
	 * Iterator implementation
	 *
	 * @return mixed
	 * @see \Iterator
	 */
	public function current() {
		return $this->data->current();
	}

	/**
	 * Iterator implementation
	 *
	 * @return mixed (scalar)
	 * @see \Iterator
	 */
	public function key() {
		return $this->data->key();
	}

	/**
	 * Iterator implementation
	 *
	 * @see \Iterator
	 */
	public function next() : void {
		$this->data->next();
	}

	/**
	 * Iterator implementation
	 *
	 * @see \Iterator
	 */
	public function rewind() : void {
		$this->data->rewind();
	}

	/**
	 * Iterator implementation
	 *
	 * @see \Iterator
	 */
	public function valid() : bool {
		return $this->data->valid();
	}
}
