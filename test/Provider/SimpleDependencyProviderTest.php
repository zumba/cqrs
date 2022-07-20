<?php

declare(strict_types=1);

namespace Zumba\CQRS\Test\Provider;

use PHPUnit\Framework\TestCase;
use Zumba\CQRS\Provider\InvalidDependency;
use Zumba\CQRS\Provider\SimpleDependencyProvider;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\EmptyConstructorCommand;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\EmptyConstructorCommandHandler;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\MultiTypeCommand;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\NonOptionalCommand;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\NotValidCommand;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\OptionalParamConstructorCommand;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\OptionalParamConstructorCommandHandler;
use Zumba\CQRS\Test\Stub\SimpleDependencyProvider\PrivateConstructorCommand;

class SimpleDependencyProviderTest extends TestCase
{
    public function testMultiTypeParam(): void
    {
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $this->markTestSkipped('Test only application to PHP 8.0+.');
        }

        $provider = new SimpleDependencyProvider();
        $dto = new MultiTypeCommand();
        $this->expectException(InvalidDependency::class);
        $this->expectExceptionMessage("Don't be a night elf! `\$multiType` has multiple types.");
        $provider->getCommandHandler($dto);
    }

    public function testNonOptionalParam(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NonOptionalCommand();
        $this->expectException(InvalidDependency::class);
        $testingClass = 'Zumba\CQRS\Test\Stub\SimpleDependencyProvider\NonOptionalParamConstructor';
        $this->expectExceptionMessage("Don't be a night elf! `{$testingClass} \$notSimple` has required params.");
        $provider->getCommandHandler($dto);
    }

    public function testNonInstantiable(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new PrivateConstructorCommand();
        $this->expectException(InvalidDependency::class);
        $testingClass = 'Zumba\CQRS\Test\Stub\SimpleDependencyProvider\PrivateConstructor';
        $this->expectExceptionMessage("Don't be a night elf! `{$testingClass} \$notSimple` cannot be instantiated.");
        $provider->getCommandHandler($dto);
    }

    public function testNonValidCommandHandler(): void
    {
        $provider = new SimpleDependencyProvider();
        $dto = new NotValidCommand();
        $this->expectException(InvalidDependency::class);
        $this->expectExceptionMessage("Don't be a night elf! `\$notValid` typehint is not a valid class.");
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
