<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

class OptionalParamConstructor
{
    public function __construct($a = 1, $b = 's')
    {
    }
}
