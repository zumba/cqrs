<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

class NonOptionalParamConstructor
{
    /** @phpstan-ignore-next-line */
    public function __construct($a, $b = 's')
    {
    }
}
