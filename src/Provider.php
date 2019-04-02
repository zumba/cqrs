<?php declare(strict_types = 1);

namespace Zumba\CQRS;

interface Provider extends \Zumba\CQRS\Query\HandlerProvider, \Zumba\CQRS\Command\HandlerProvider{
}
