<?php

namespace Zumba\CQRS\Query\Provider;

use \Zumba\CQRS\Query\Query,
	\Zumba\CQRS\Query\Handler,
	\Zumba\CQRS\Query\HandlerFactory;

/**
 * SimpleProvider attempts to build the handler with no dependencies by calling new
 */
class SimpleProvider implements \Zumba\CQRS\Query\Provider {

	/**
	 * Build a Query handler.
	 */
	public function getHandler(Query $query) : ? Handler {
		$handlerName = get_class($query) . "Handler";
		if (!class_exists($handlerName)) {
			return null;
		}
		$image = new \ReflectionClass($handlerName);
		$constructor = $image->getConstructor();
		if ($constructor !== null) {
			return null;
		}
		$handler = new $handlerName();
		if ($handler instanceof Handler) {
			return $handler;
		}
		return null;
	}
}
