<?php

namespace Zumba\CQRS\Command\Provider;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\HandlerFactory;

/**
 * ClassProvider attempts to load a \Zumba\CQRS\Command\HandlerFactory
 */
class ClassProvider implements \Zumba\CQRS\Command\Provider {

	/**
	 * Locate the command handler factory and make the handler
	 *
	 * @throws \LogicException if a handler factory class does not implement HandlerFactory
	 */
	public function getHandler(Command $command) : ? Handler {
		$factory = get_class($command) . "HandlerFactory";
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
