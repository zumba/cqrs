<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query\Response;

use JsonSerializable;
use Zumba\CQRS\Query\QueryResponse;

final class Scalar extends QueryResponse implements JsonSerializable, Success
{
    /**
     * @var mixed any scalar value (integer|float|string|boolean)
     */
    protected $value;

    /**
     * Create a new Scalar response.
     *
     * Use \Zumba\CQRS\Query\QueryResponse::fromScalar to create this response object.
     *
     * @param mixed $value Any scalar value (integer|float|string|boolean)
     * @throws \Zumba\CQRS\InvalidResponse if the value is not scalar.
     * @see \Zumba\CQRS\Query\QueryResponse::fromScalar
     */
    protected static function from($value): Scalar
    {
        if (!is_scalar($value)) {
            throw new \Zumba\CQRS\InvalidResponse(
                sprintf("Value passed to %s must be scalar, %s received", __METHOD__, gettype($value))
            );
        }
        $response = new static();
        $response->value = $value;
        return $response;
    }

    /**
     * Get the scalar value
     *
     * @return mixed any scalar value (integer|float|string|boolean)
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * JsonSerializable implementation
     *
     * @see \JsonSerializable
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
