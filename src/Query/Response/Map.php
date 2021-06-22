<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query\Response;

class Map extends \Zumba\CQRS\Query\QueryResponse implements \JsonSerializable, \ArrayAccess, Success
{

    /**
     * @var array
     */
    protected $data;

    /**
     * Create a new Map response.
     *
     * Use \Zumba\CQRS\Query\QueryResponse::fromMap to create this response object.
     *
     * @see \Zumba\CQRS\Query\QueryResponse::fromMap
     */
    protected static function fromArray(array $data): Map
    {
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
    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this->data) ?: '';
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     * @see \ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     * @return mixed
     * @see \ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws \Zumba\CQRS\InvalidResponse
     * @see \ArrayAccess
     */
    public function offsetSet($offset, $value): void
    {
        throw new \Zumba\CQRS\InvalidResponse(static::class . " is not mutable.");
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     * @throws \Zumba\CQRS\InvalidResponse
     * @see \ArrayAccess
     */
    public function offsetUnset($offset): void
    {
        throw new \Zumba\CQRS\InvalidResponse(static::class . " is not mutable.");
    }
}
