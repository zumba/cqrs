<?php

namespace Zumba\CQRS;

interface HandlerFactory {

	/**
	 * Build a Handler
	 */
	public static function make() : Handler;
}
