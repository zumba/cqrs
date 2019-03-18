<?php

namespace Zumba\Test\CQRS\Provider;

use \Zumba\CQRS\Provider\ModelProvider,
	\Zumba\CQRS\DTO,
	\Zumba\CQRS\Response;

class Bar extends DTO {}
class BarHandler implements \Zumba\CQRS\Handler {
	public function __construct(Foo $notModel) {}
	public function handle(DTO $dto) : Response {}
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
		$provider->getHandler($dto);
	}
}
