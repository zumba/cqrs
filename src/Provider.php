<?php

namespace Zumba\CQRS;

interface Provider extends \Zumba\CQRS\Query\HandlerProvider, \Zumba\CQRS\Command\HandlerProvider{
}
