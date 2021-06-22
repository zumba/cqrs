<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query\Response;

class Iterator extends \Zumba\CQRS\Query\QueryResponse implements \Iterator, \JsonSerializable, \Countable, Success
{

    /**
     * @var \Iterator
     */
    protected $data;

    /**
     * Data that has been expanded from the iterator/generator.
     *
     * @var array
     */
    protected $iteratedData = [];

    /**
     * Internal count that is used when the iterator is iterated via external mechanism.
     *
     * @var int
     */
    protected $internalCount = 0;

    /**
     * Create a new Iterator response from an array
     *
     * Use \Zumba\CQRS\Query\QueryResponse::fromList to create this response object.
     *
     * @see \Zumba\CQRS\Query\QueryResponse::fromList
     */
    protected static function fromArray(array $data): Iterator
    {
        $response = new static();
        $response->data = new \ArrayIterator($data);
        return $response;
    }

    /**
     * Create a new Iterator response from an \Iterator (e.g. a Generator)
     *
     * Use \Zumba\CQRS\Query\QueryResponse::fromIterator to create this response object.
     *
     * @see \Zumba\CQRS\Query\QueryResponse::fromIterator
     */
    public static function fromIterator(\Iterator $data): Iterator
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
        return $this->iterateData();
    }

    public function __toString(): string
    {
        if ($this->data->valid()) {
            return json_encode($this->jsonSerialize()) ?: '';
        }
        return "Invalid Iterator.";
    }

    /**
     * Retrieve iterator data into array.
     *
     * @return array[int][mixed]
     */
    private function iterateData(): array
    {
        if (empty($this->iteratedData) && $this->valid()) {
            $this->iteratedData = iterator_to_array($this->data);
        }
        return $this->iteratedData;
    }

    /**
     * Gives the count of the iterator.
     */
    public function count(): int
    {
        if (!$this->valid() && empty($this->iteratedData)) {
            return $this->internalCount;
        }
        return count($this->iterateData());
    }

    /**
     * Iterator implementation
     *
     * @return mixed
     * @see \Iterator
     */
    public function current()
    {
        return $this->data->current();
    }

    /**
     * Iterator implementation
     *
     * @return mixed (scalar)
     * @see \Iterator
     */
    public function key()
    {
        return $this->data->key();
    }

    /**
     * Iterator implementation
     *
     * @see \Iterator
     */
    public function next(): void
    {
        $this->data->next();
        $this->internalCount++;
    }

    /**
     * Iterator implementation
     *
     * @see \Iterator
     */
    public function rewind(): void
    {
        $this->data->rewind();
        $this->internalCount = 0;
    }

    /**
     * Iterator implementation
     *
     * @see \Iterator
     */
    public function valid(): bool
    {
        return $this->data->valid();
    }
}
