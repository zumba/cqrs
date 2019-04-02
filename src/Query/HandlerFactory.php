<?php declare(strict_types = 1);

namespace Zumba\CQRS\Query;

interface HandlerFactory {

	/**
	 * Build a Handler
	 */
	public static function make() : Handler;
}
