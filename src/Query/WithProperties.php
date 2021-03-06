<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query;

interface WithProperties
{
    /**
     * Create a Query from an array of data.
     *
     * @param array<string, mixed> $props
     */
    public static function fromArray(array $props): Query;
}
