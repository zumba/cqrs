<?php

namespace Zumba\CQRS\Command\Provider;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Handler,
	\Zumba\CQRS\Command\HandlerFactory\ModelInjector;

/**
 * ModelProvider attempts to build the handler by injecting models into the constructor.
 */
class ModelProvider implements \Zumba\CQRS\Command\Provider {

	/**
	 * Build a command handler by attempting to inject models.
	 */
	public function getHandler(Command $command) : ? Handler {
		try {
			return ModelInjector::make(get_class($command) . "Handler");
		} catch (\LogicException $e) {
			// if there was a logic exception, then the developer did something wrong so we should
			// bubble it so they get a nice error message.
			throw $e;
		} catch (\Throwable $e) {
			// Any other problem should just return null.  This way some other provider might
			// be able to build the handler.
			return null;
		}
	}
}
