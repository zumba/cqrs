<?php

namespace Zumba\CQRS\Command\Provider;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\HandlerFactory;

/**
 * SimpleProvider attempts to build the handler with no dependencies by calling new
 */
class SimpleProvider implements \Zumba\CQRS\Command\Provider {

	/**
	 * Build a command handler.
	 */
	public function getHandler(Command $command) : ? Handler {
		$handlerName = get_class($command) . "Handler";
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
