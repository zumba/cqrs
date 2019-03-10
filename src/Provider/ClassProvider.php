<?php

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Handler,
	\Zumba\CQRS\HandlerFactory;

/**
 * ClassProvider attempts to load a \Zumba\CQRS\HandlerFactory
 */
class ClassProvider implements \Zumba\CQRS\Provider {

	/**
	 * Locate the dto handler factory and make the handler
	 *
	 * @throws \LogicException if a handler factory class does not implement HandlerFactory
	 */
	public function getHandler(DTO $dto) : ? Handler {
		$factory = get_class($dto) . "HandlerFactory";
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
