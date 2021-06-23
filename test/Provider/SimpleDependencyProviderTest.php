<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Provider;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Provider\InvalidDependency;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\EmptyConstructorCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\EmptyConstructorCommandHandler;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\NonOptionalCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\NotValidCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\OptionalParamConstructorCommand;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\OptionalParamConstructorCommandHandler;
use Zumba\CQRS\Test\Fixture\SimpleDependencyProvider\PrivateConstructorCommand;

class SimpleDependencyProviderTest extends TestCase
{
    /**
     * @var int
     */
    private $existingErrorLevel;

    public function setUp(): void
    {
        parent::setUp();
        $this->existingErrorLevel = error_reporting();
        // for now, ignore deprecated errors: to avoid error in most of the tests:
        // Method ReflectionParameter::getClass() is deprecated
        // todo: refactor to not use getClass anymore, then remove this work-around
        error_reporting($this->existingErrorLevel & ~E_DEPRECATED);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        // restore original error reporting level
        error_reporting($this->existingErrorLevel);
    }

    public function testNonOptionalParam(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NonOptionalCommand();
        $this->expectException(InvalidDependency::class);
        $provider->getCommandHandler($dto);
    }

    public function testNonInstantiable(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new PrivateConstructorCommand();
        $this->expectException(InvalidDependency::class);
        $provider->getCommandHandler($dto);
    }

    public function testNonValidCommandHandler(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NotValidCommand();
        $this->expectException(InvalidDependency::class);
        $provider->getCommandHandler($dto);
    }

    public function testEmptyConstructor(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new EmptyConstructorCommand();
        $this->assertInstanceOf(EmptyConstructorCommandHandler::class, $provider->getCommandHandler($dto));
    }

    public function testOptionalParamConstructor(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new OptionalParamConstructorCommand();
        $this->assertInstanceOf(OptionalParamConstructorCommandHandler::class, $provider->getCommandHandler($dto));
    }
}
