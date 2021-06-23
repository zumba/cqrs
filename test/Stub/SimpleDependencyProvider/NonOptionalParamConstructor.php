<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\SimpleDependencyProvider;

class NonOptionalParamConstructor
{
    /** @phpstan-ignore-next-line */
    public function __construct($a, $b = 's')
    {
    }
}
