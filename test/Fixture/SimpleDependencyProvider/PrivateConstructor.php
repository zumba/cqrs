<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

final class PrivateConstructor
{
    private function __construct()
    {
    }

    public static function getInstance(): PrivateConstructor
    {
        return new static();
    }
}
