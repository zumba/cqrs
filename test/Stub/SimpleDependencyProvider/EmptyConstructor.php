<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Stub\SimpleDependencyProvider;

class EmptyConstructor
{
    /** @phpstan-ignore-next-line */
    public function funcA($arg1, $arg2): void
    {
    }
}
