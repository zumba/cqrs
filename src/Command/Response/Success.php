<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command\Response;

use JsonSerializable;
use Zumba\CQRS\Command\CommandResponse;

final class Success extends CommandResponse implements JsonSerializable
{
    /**
     * Create a new Success response.
     *
     * Use \Zumba\CQRS\Command\Response::ok() to create this response object.
     *
     * @see \Zumba\CQRS\Command\Response::ok
     */
    protected static function make(): Success
    {
        return new static();
    }

    /**
     * JsonSerializable implementation
     *
     * @see \JsonSerializable
     * @return array<null> Empty array
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
