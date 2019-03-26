<?php

namespace Zumba\Test\CQRS\Provider;

use \Zumba\CQRS\Provider\ModelProvider,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\CommandResponse;

class Bar extends Command {}
class BarHandler implements \Zumba\CQRS\Command\Handler {
	public function __construct(Foo $notModel) {}
	public function handle(Command $command) : CommandResponse {}
}

/**
 * @group cqrs
 */
class ModelProviderTest extends \Zumba\Service\Test\TestCase {

	/**
	 * @expectedException \Zumba\CQRS\Provider\InvalidDependency
	 */
	public function testGetHandler() {
		$provider = new ModelProvider();
		$dto = new Bar();
		$provider->getCommandHandler($dto);
	}
}
