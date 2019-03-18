<?php

namespace Zumba\Test\CQRS\Provider;

use \Zumba\CQRS\Provider\ClassProvider,
	\Zumba\CQRS\DTO;

class Foo extends DTO {}
class FooHandlerFactory {}

/**
 * @group cqrs
 */
class ClassProviderTest extends \Zumba\Service\Test\TestCase {
	public function testGetHandler() {
		$provider = new ClassProvider();
		$dto = $this->getMockBuilder(DTO::class)->getMock();
		$this->assertNull($provider->getHandler($dto));
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidHandler
	 */
	public function testGetHandlerThrowsIfNotImplemented() {
		$provider = new ClassProvider();
		$dto = new Foo();
		$provider->getHandler($dto);
	}
}
