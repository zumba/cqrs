<?php

namespace Zumba\CQRS\Provider;

use \Zumba\CQRS\DTO,
	\Zumba\CQRS\Handler,
	\Zumba\CQRS\HandlerFactory;

/**
 * MethodProvider attempts to use a factory method on the handler itself.
 */
class MethodProvider implements \Zumba\CQRS\Provider {

	/**
	 * Locate the dto handler and if it implements HandlerFactory, make it.
	 */
	public function getHandler(DTO $dto) : ? Handler {
		$factory = get_class($dto) . "Handler";
		if (!class_exists($factory) || !in_array(HandlerFactory::class, class_implements($factory))) {
			return null;
		}
		return $factory::make();
	}
}
