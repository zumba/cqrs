<?php declare(strict_types = 1);

namespace Zumba\Test\CQRS\Provider;

use \Zumba\CQRS\Provider\SimpleDependencyProvider,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse;

class NonOptionalParamConstructor {
	public function __construct($a, $b = 's') {}
}
class NonOptionalCommand extends Command {}
class NonOptionalCommandHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(NonOptionalParamConstructor $notSimple) {}
	public function handle(Command $command) : CommandResponse {}
}

class PrivateConstructor {
	private function __construct() {}

	public static function getInstance() {
		new static();
	}
}
class PrivateConstructorCommand extends Command {}
class PrivateConstructorCommandHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(PrivateConstructor $notSimple) {}
	public function handle(Command $command) : CommandResponse {}
}

class NotValidCommand extends Command {}
class NotValidCommandHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(string $notValid) {}
	public function handle(Command $command) : CommandResponse {}
}

class EmptyContructor {
   function funcA($arg1, $arg2) {}
}
class EmptyContructorCommand extends Command {}
class EmptyContructorCommandHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(EmptyContructor $simpleDependency) {}
	public function handle(Command $command) : CommandResponse {}
}

class OptionalParamConstructor {
	public function __construct($a = 1, $b = 's') {}
}
class OptionalParamConstructorCommand extends Command {}
class OptionalParamConstructorCommandHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(OptionalParamConstructor $simpleDependency) {}
	public function handle(Command $command) : CommandResponse {}
}

/**
 * @group cqrs
 */
class SimpleDependencyProviderTest extends \Zumba\Service\Test\TestCase {

	/**
	 * @expectedException \Zumba\CQRS\Provider\InvalidDependency
	 */
	public function testNonOptionalParam() {
		$provider = new SimpleDependencyProvider();
		$dto = new NonOptionalCommand();
		$provider->getCommandHandler($dto);
	}

	/**
	 * @expectedException \Zumba\CQRS\Provider\InvalidDependency
	 */
	public function testNonInstantiable() {
		$provider = new SimpleDependencyProvider();
		$dto = new PrivateConstructorCommand();
		$provider->getCommandHandler($dto);
	}

	/**
	 * @expectedException \Zumba\CQRS\Provider\InvalidDependency
	 */
	public function testNonValidCommandHandler() {
		$provider = new SimpleDependencyProvider();
		$dto = new NotValidCommand();
		$provider->getCommandHandler($dto);
	}

	public function testEmptyConstructor() {
		$provider = new SimpleDependencyProvider();
		$dto = new EmptyContructorCommand();
		$this->assertInstanceOf(EmptyContructorCommandHandler::class, $provider->getCommandHandler($dto));
	}

	public function testOptionalParamConstructor() {
		$provider = new SimpleDependencyProvider();
		$dto = new OptionalParamConstructorCommand();
		$this->assertInstanceOf(OptionalParamConstructorCommandHandler::class, $provider->getCommandHandler($dto));
	}
}
