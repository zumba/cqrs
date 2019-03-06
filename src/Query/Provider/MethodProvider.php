<?php

namespace Zumba\CQRS\Query\Provider;

use \Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\Handler,
	\Zumba\CQRS\Query\HandlerFactory;

/**
 * MethodProvider attempts to use a factory method on the handler itself.
 */
class MethodProvider implements \Zumba\CQRS\Query\Provider {

	/**
	 * Locate the Query handler and if it implements HandlerFactory, make it.
	 */
	public function getHandler(Query $query) : ? Handler {
		$factory = get_class($query) . "Handler";
		if (!class_exists($factory) || !in_array(HandlerFactory::class, class_implements($factory))) {
			return null;
		}
		return $factory::make();
	}
}
