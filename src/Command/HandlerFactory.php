<?php

namespace Zumba\CQRS\Command;

interface HandlerFactory {

	/**
	 * Build a Handler
	 */
	public static function make() : Handler;
}
