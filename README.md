# Zumba ***CQRS***

[Command Query Responsibility Segregation (*CQRS*)][1]
is an established design pattern.  This library aims to implement the *plumbing* needed to create CQRS commands and
queries.

---

- [Command Bus Usage][3]
  - [Creating a command][4]
  - [Creating a command handler][5]
  - [Dispatching a command][6]
- [Query Bus Usage][7]
  - [Creating a query handler][8]
  - [Dispatching a query][9]
- [Providers][10]
  - [Class provider][11]
  - [Method provider][12]
  - [Simple dependency provider][13]
  - [Create a custom provider][14]
- [Middleware][15]
  - [Logger middleware][16]
  - [Custom middleware][17]

---
## Command Bus Usage

The command bus allows commands with handlers to be dispatched with the result being either a `Success` or `Failure`.

By design, the command must not return any data.

### Creating a command

Creating a command DTO to dispatch to the command bus requires us to extend the abstract `Command` class.

```php
<?php

namespace My\Command;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\WithProperties;

final class MyCommand extends Command implements WithProperties
{
    protected string $message;

    public static function fromArray(array $props): Command
    {
        $command = new static();
        $command->message = $props['message'];
        return $command;
    }
}
```

### Creating a command handler

With the `MyCommand` created from the [Creating a command][4] section, we need to handle this command to perform an action.

> Tip: The command handler is a great place to introduce any dependencies to be injected.

```php
<?php

namespace My\Command;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\CommandService;

final class MyCommandHandler implements Handler
{
    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
        echo $command->message;
        return CommandResponse::fromSuccess();
    }
}
```

### Dispatching a command

With both a command and handler for that command created, we're ready to dispatch our command to the command bus.

A default batteries-included command bus can be instanced that we can dispatch our command DTOs to the appropriate handlers.

```php
<?php

use My\Command\MyCommand;
use Zumba\CQRS\CommandBus;
use Zumba\CQRS\Response\Success;

$commandBus = CommandBus::defaultBus();
$command = MyCommand::fromProperties([
    'message' => 'Hello!',
]);

$result = $commandBus->dispatch($command);
// Hello! is echoed
var_dump($result instanceof Success);
// true
```

---

## Query Bus Usage

Similar to the command bus, the query bus operates with a query DTO with an accompanying handler dispatched to the query bus in order to get resulting data.

### Creating a query

Creating a query DTO to dispatch to the query bus requires us to extend the abstract `Query` class.

```php
<?php

namespace My\Query;

use Zumba\CQRS\Query\Query;
use Zumba\CQRS\Query\WithProperties;

final class MyQuery extends Query implements WithProperties
{
    protected string $ID;

    public static function fromArray(array $props): Query
    {
        $query = new static();
        $query->ID = $props['id'];
        return $query;
    }
}
```

### Create a query handler

Query handlers are designed to return a query response depending on the kind of data to return.

In the above created `MyQuery` DTO, let's assume that this is retrieving a key-value result for a specific entity.
We would build our response using the `Map` response type:

```php
<?php

namespace My\Query;

use Zumba\CQRS\Query\Handler;
use Zumba\CQRS\Query\Query;
use Zumba\CQRS\Query\QueryResponse;

final class MyQueryHandler implements Handler
{
    public function handle(Query $query): QueryResponse
    {
        // Do appropriate lookups
        return QueryResponse::fromMap([
            'id' => 'example-id',
            'name' => 'example-name',
        ]);
    }
}
```

### Dispatching a query

As with [dispatching a command][6], we can use the batteries-included query bus which can be included using the `QueryBusTrait`.

```php
namespace My\Query;

use Zumba\CQRS\QueryBusTrait;

class Example {
    use QueryBusTrait;

    public function query(): array
    {
        $response = $this->queryBus()->dispatch(new MyQuery([
            'id' => 'example-id',
        ]));
        return [
            'id' => $response['id'],
            'name' => $response['name'],
        ];
    }

}

var_dump((new Example)->query());
// [
//     'id' => 'example-id',
//     'name' => 'example-name',
// ]
```

## Providers

Providers offer the ability for the command bus to be able to supply a handler for a DTO dispatched to the bus.

`zumba\cqrs` provides several providers out of the box.

### Class Provider

The `Zumba\CQRS\Provider\ClassProvider` can be used to construct handlers for a DTO via a handler factory.
This can be used when the handlers have complex dependencies (ie dependencies that require configuration).

To utilize this, create a handler factory in the same namespace as the DTO. Assume our DTO from the [create a command][4] section
of `MyCommand`, the handler factory would look like this:

```php
<?php

namespace My\Command;

use Zumba\CQRS\Command\HandlerFactory;
use Zumba\CQRS\Command\Handler;

class MyCommandHandlerFactory implements HandlerFactory
{
    public static function make(): Handler
    {
        return new MyCommandHandler();
    }
}
```

### Method Provider

The `Zumba\CQRS\Provider\MethodProvider` is similar to the `ClassProvider` except the handler itself can serve as its own factory.

The `MyCommandHandler` can be adapted from the [create a command handler][5] section:

```php
<?php

namespace My\Command;

use Zumba\CQRS\Command\Command;
use Zumba\CQRS\Command\CommandResponse;
use Zumba\CQRS\Command\Handler;
use Zumba\CQRS\Command\HandlerFactory;
use Zumba\CQRS\CommandService;

final class MyCommandHandler implements Handler, HandlerFactory
{
    public function handle(Command $command, CommandService $commandService): CommandResponse
    {
        echo $command->message;
        return CommandResponse::fromSuccess();
    }

    public static function make(): Handler
    {
        return new static();
    }
}
```

### Simple Dependency Provider

In cases where all dependencies can be instantiated without parameters, the `Zumba\CQRS\Provider\SimpleDependencyProvider` can be used to construct the handler without the need of a `HandlerFactory`.

### Create a custom provider

To create your own custom provider, implement the `Zumba\CQRS\Provider` interface and create the bus with the custom provider.
You can also provide additional providers to fallback on if your CustomProvider can't accommodate the DTO.

```php
<?php

use My\Provider\CustomProvider;

$bus = CommandBus::fromProviders(
    new CustomProvider(),
    new ClassProvider(),
);
```

---

## Middleware

The CQRS library is equipped to allow for middleware to be attached to a command or query bus to allow for generic actions to occur for any or all DTOs.

### Logger middleware

The logger middleware allows logging all DTOs that are dispatched to through a bus.

```php
<?php

use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Zumba\CQRS\CommandBus;
use Zumba\CQRS\MiddlewarePipeline;
use Zumba\CQRS\Middleware\Logger;

$bus = CommandBus::defaultBus();
// Substitute with a real Psr logger.
$logger = new NullLogger();

$middlewarePipeline = MiddlewarePipeline::fromMiddleware(
    Logger::fromLoggerAndLevel(
        $logger,
        LogLevel::INFO,
    )
);

$bus = $bus->withMiddleware($middlewarePipeline);
```

### Custom middleware

Custom middleware can be included in the `MiddlewarePipeline` as long as it implements the `Middleware` interface.

The `handle` method will accept a DTO and a `callable` to which to continue the process.

See the [`Logger`][2] middleware as an example.


[1]: https://docs.microsoft.com/en-us/azure/architecture/patterns/cqrs
[2]: https://github.com/zumba/cqrs/blob/2b1c5227452337f550266dc1bb1217ff7a40ef28/src/Middleware/Logger.php
[3]: #command-bus-usage
[4]: #creating-a-command
[5]: #creating-a-command-handler
[6]: #dispatching-a-command
[7]: #query-bus-usage
[8]: #create-a-query-handler
[9]: #dispatching-a-query
[10]: #providers
[11]: #class-provider
[12]: #method-provider
[13]: #simple-dependency-provider
[14]: #create-a-custom-provider
[15]: #middleware
[16]: #logger-middleware
[17]: #custom-middleware
