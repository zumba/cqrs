<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query\Response;

use JsonSerializable;
use Throwable;
use Zumba\CQRS\Query\QueryResponse;
use Zumba\CQRS\Query\Response\Failure as ResponseFailure;

final class Failure extends QueryResponse implements JsonSerializable
{
    /**
     * @var \Throwable
     */
    protected $error;

    /**
     * @var array<string, mixed>
     */
    protected $meta = [];

    /**
     * Create a new Failure response from a Throwable.
     *
     * Use \Zumba\CQRS\Query\QueryResponse::fromThrowable() to create this response object.
     *
     * @see \Zumba\CQRS\Query\QueryResponse::fromThrowable
     */
    protected static function make(Throwable $error): Failure
    {
        $response = new static();
        $response->error = $error;
        return $response;
    }

    /**
     * Get the Throwable.
     */
    public function getError(): Throwable
    {
        return $this->error;
    }

    /**
     * JsonSerializable implementation
     *
     * @see \JsonSerializable
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $error = $this->error->getMessage();
        return compact('error');
    }

    /**
     * Get meta data associated to the failure.
     *
     * @return array<string, mixed>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Failure instance with additional meta data.
     *
     * @param array<string, mixed> $meta
     */
    public function withMeta(array $meta): ResponseFailure
    {
        $failure = clone $this;
        $failure->meta = $meta;
        return $failure;
    }
}
