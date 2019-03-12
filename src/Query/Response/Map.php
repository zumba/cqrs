<?php

namespace Zumba\CQRS\Query\Response;

class Map extends \Zumba\CQRS\Query\QueryResponse implements \JsonSerializable, \ArrayAccess {

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * Create a new Map response.
	 *
	 * Use \Zumba\CQRS\Query\QueryResponse::map to create this response object.
	 *
	 * @see \Zumba\CQRS\Query\QueryResponse::map
	 */
	protected static function fromArray(array $value) : Map {
		$response = new static();
		$response->data = $data;
		return $response;
	}

	/**
	 * JsonSerializable implementation
	 *
	 * @see \JsonSerializable
	 * @return mixed
	 */
	public function jsonSerialize() {
		return $this->data;
	}

	public function __toString() : string {
		return implode(",", $this->data);
	}

	/**
	 * ArrayAccess implementation
	 *
	 * @param mixed $offset
	 * @see \ArrayAccess
	 */
	public function offsetExists($offset) : bool {
		return isset($this->data[$offset]);
	}

	/**
	 * ArrayAccess implementation
	 *
	 * @param mixed $offset
	 * @return mixed
	 * @see \ArrayAccess
	 */
	public function offsetGet($offset) {
		return $this->data[$offset] ?? null;
	}

	/**
	 * ArrayAccess implementation
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 * @see \ArrayAccess
	 */
	public function offsetSet($offset, $value) : void {
		$this->data[$offset] = $value;
	}

	/**
	 * ArrayAccess implementation
	 *
	 * @param mixed $offset
	 * @see \ArrayAccess
	 */
	public function offsetUnset($offset) : void {
		unset($this->data[$offset]);
	}
}
