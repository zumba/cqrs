<?php

namespace Zumba\CQRS\Command;

use \Zumba\Primer\Base\Model;

interface HandlerFactory {

	/**
	 * Build a Handler
	 */
	public static function make() : Handler;
}
