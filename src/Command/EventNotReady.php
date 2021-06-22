<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

final class EventNotReady extends \RuntimeException
{

    /**
     * The maximum amount of deferment time.
     *
     * In `strtotime` format: IE +1 days
     *
     * @var string
     */
    private $maximumDelay;

    /**
     * Constructor.
     */
    public function __construct(string $message, string $maximumDelay)
    {
        $this->message = $message;
        $this->maximumDelay = $maximumDelay;
        parent::__construct($message);
    }

    /**
     * Get the maximum delay for deferment.
     */
    public function maximumDelay(): string
    {
        return $this->maximumDelay;
    }
}
