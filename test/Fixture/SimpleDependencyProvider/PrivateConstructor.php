<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Fixture\SimpleDependencyProvider;

class PrivateConstructor
{
    private function __construct()
    {
    }

    public static function getInstance()
    {
        new static();
    }
}
