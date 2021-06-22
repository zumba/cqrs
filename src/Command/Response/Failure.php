<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command\Response;

class Failure extends \Zumba\CQRS\Command\CommandResponse implements \JsonSerializable
{

    /**
     * @var \Throwable
     */
    protected $error;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * Create a new Failure response from a Throwable.
     *
     * Use \Zumba\CQRS\Command\CommandResponse::fromThrowable() to create this response object.
     *
     * @see \Zumba\CQRS\Command\CommandResponse::fromThrowable
     */
    protected static function make(\Throwable $error): Failure
    {
        $response = new static();
        $response->error = $error;
        return $response;
    }

    /**
     * Get the Throwable.
     */
    public function getError(): \Throwable
    {
        return $this->error;
    }

    /**
     * Get meta data associated to the failure.
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Failure instance with additional meta data.
     */
    public function withMeta(array $meta): Failure
    {
        $failure = clone $this;
        $failure->meta = $meta;
        return $failure;
    }

    /**
     * JsonSerializable implementation
     *
     * @see \JsonSerializable
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'error' => $this->error->getMessage(),
            'meta' => $this->meta
        ];
    }
}
