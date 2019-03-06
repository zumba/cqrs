<?php

namespace Zumba\CQRS\Query;

interface HandlerFactory {

	/**
	 * Build a Handler
	 */
	public static function make() : Handler;
}
