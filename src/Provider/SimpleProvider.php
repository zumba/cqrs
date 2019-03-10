<?php

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Handler,
	\Zumba\CQRS\HandlerFactory;

/**
 * SimpleProvider attempts to build the handler with no dependencies by calling new
 */
class SimpleProvider implements \Zumba\CQRS\Provider {

	/**
	 * Build a dto handler.
	 */
	public function getHandler(DTO $dto) : ? Handler {
		$handlerName = get_class($dto) . "Handler";
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
