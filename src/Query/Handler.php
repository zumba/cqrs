<?php

declare(strict_types=1);

namespace Zumba\CQRS\Query;

interface Handler
{

    /**
     * Handle a Query
     */
    public function handle(Query $dto): QueryResponse;
}
