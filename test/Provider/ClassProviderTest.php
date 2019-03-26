<?php

namespace Zumba\Test\CQRS\Provider;

use \Zumba\CQRS\Provider\ClassProvider,
	\Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Query\Query;

class Foo extends Command {}
class FooQuery extends Query {}
class FooHandlerFactory {}
class FooQueryHandlerFactory {}

/**
 * @group cqrs
 */
class ClassProviderTest extends \Zumba\Service\Test\TestCase {
	public function testGetHandler() {
		$provider = new ClassProvider();
		$command = $this->getMockBuilder(Command::class)->getMock();
		try {
			$provider->getCommandHandler($this->getMockBuilder(Command::class)->getMock());
		} catch(\Exception $e) {
			$this->assertInstanceOf(\Zumba\CQRS\HandlerNotFound::class, $e);
		}
		try {
			$provider->getQueryHandler($this->getMockBuilder(Query::class)->getMock());
		} catch(\Exception $e) {
			$this->assertInstanceOf(\Zumba\CQRS\HandlerNotFound::class, $e);
		}
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidHandler
	 */
	public function testGetHandlerThrowsIfNotImplemented() {
		$provider = new ClassProvider();
		$command = new Foo();
		$provider->getCommandHandler($command);
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidHandler
	 */
	public function testGetHandlerThrowsIfNotImplementedQuery() {
		$provider = new ClassProvider();
		$command = new FooQuery();
		$provider->getQueryHandler($command);
	}
}
