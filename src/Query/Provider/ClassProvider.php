<?php

namespace Zumba\CQRS\Query\Provider;

use \Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\Handler,
	\Zumba\CQRS\Query\HandlerFactory;

/**
 * ClassProvider attempts to load a \Zumba\CQRS\Query\HandlerFactory
 */
class ClassProvider implements \Zumba\CQRS\Query\Provider {

	/**
	 * Locate the Query handler factory and make the handler
	 *
	 * @throws \LogicException if a handler factory class does not implement HandlerFactory
	 */
	public function getHandler(Query $query) : ? Handler {
		$factory = get_class($query) . "HandlerFactory";
		if (!class_exists($factory)) {
			return null;
		}
		if (in_array(HandlerFactory::class, class_implements($factory))) {
			return $factory::make();
		}
		throw new \LogicException(
			"$factory exists, but it does not implement " . HandlerFactory::class
		);
	}
}
