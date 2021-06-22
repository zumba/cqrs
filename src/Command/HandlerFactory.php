<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

interface HandlerFactory
{

    /**
     * Build a Handler
     */
    public static function make(): Handler;
}
