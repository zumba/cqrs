<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

class NonOptionalParamConstructor
{
    public function __construct($a, $b = 's')
    {
    }
}
