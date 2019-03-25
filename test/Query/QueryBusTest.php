<?php

namespace Zumba\Test\CQRS\Query;

use \Zumba\CQRS\QueryBus,
	\Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\QueryResponse,
	\Zumba\CQRS\Response;

/**
 * @group cqrs
 * @group query
 */
class QueryBusTest extends \Zumba\Service\Test\TestCase {
	public function testDispatch() {
		$dtoBus = $this->getMockBuilder(\Zumba\CQRS\Bus::class)->disableOriginalConstructor()->getMock();
		$response = $this->getMockBuilder(QueryResponse::class)->disableOriginalConstructor()->getMock();
		$query = $this->getMockBuilder(Query::class)->getMock();
		$dtoBus
			->expects($this->once())
			->method('dispatch')
			->with($query)
			->will($this->returnValue($response));

		$bus = QueryBus::fromBus($dtoBus);
		$this->assertSame($response, $bus->dispatch($query));
	}

	/**
	 * @expectedException \Zumba\CQRS\InvalidResponse
	 */
	public function testDispatchFailed() {
		$dtoBus = $this->getMockBuilder(\Zumba\CQRS\Bus::class)->disableOriginalConstructor()->getMock();
		$response = $this->getMockBuilder(Response::class)->getMock();
		$query = $this->getMockBuilder(Query::class)->getMock();
		$dtoBus
			->expects($this->once())
			->method('dispatch')
			->with($query)
			->will($this->returnValue($response));

		$bus = QueryBus::fromBus($dtoBus);
		$bus->dispatch($query);
	}
}
