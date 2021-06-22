<?php

declare(strict_types=1);

namespace Zumba\CQRS\Command;

use Zumba\Primer\Model\EventQueue\Event;

interface WithEvent
{

    public static function fromEvent(Event $event): Command;
}
