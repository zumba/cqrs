<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Provider;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\EmptyConstructorCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\NonOptionalCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\NotValidCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\OptionalParamConstructorCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\PrivateConstructorCommand;

class SimpleDependencyProviderTest extends TestCase
{
    /**
     * @expectedException \Zumba\CQRS\Provider\InvalidDependency
     */
    public function testNonOptionalParam()
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NonOptionalCommand();
        $provider->getCommandHandler($dto);
    }

    /**
     * @expectedException \Zumba\CQRS\Provider\InvalidDependency
     */
    public function testNonInstantiable()
    {
        $provider = new SimpleDependencyProvider();
        $dto = new PrivateConstructorCommand();
        $provider->getCommandHandler($dto);
    }

    /**
     * @expectedException \Zumba\CQRS\Provider\InvalidDependency
     */
    public function testNonValidCommandHandler()
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NotValidCommand();
        $provider->getCommandHandler($dto);
    }

    public function testEmptyConstructor()
    {
        $provider = new SimpleDependencyProvider();
        $dto = new EmptyConstructorCommand();
        $this->assertInstanceOf(EmptyConstructorCommandHandler::class, $provider->getCommandHandler($dto));
    }

    public function testOptionalParamConstructor()
    {
        $provider = new SimpleDependencyProvider();
        $dto = new OptionalParamConstructorCommand();
        $this->assertInstanceOf(OptionalParamConstructorCommandHandler::class, $provider->getCommandHandler($dto));
    }
}
