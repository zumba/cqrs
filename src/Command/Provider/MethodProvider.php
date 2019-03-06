<?php

namespace Zumba\CQRS\Command\Provider;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\HandlerFactory;

/**
 * MethodProvider attempts to use a factory method on the handler itself.
 */
class MethodProvider implements \Zumba\CQRS\Command\Provider {

	/**
	 * Locate the command handler and if it implements HandlerFactory, make it.
	 */
	public function getHandler(Command $command) : ? Handler {
		$factory = get_class($command) . "Handler";
		if (!class_exists($factory) || !in_array(HandlerFactory::class, class_implements($factory))) {
			return null;
		}
		return $factory::make();
	}
}
