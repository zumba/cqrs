<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\SimpleDependencyProvider;

class OptionalParamConstructor
{
    /** @phpstan-ignore-next-line */
    public function __construct($a = 1, $b = 's')
    {
    }
}
