<?php

namespace Zumba\CQRS\Command\Middleware;

use \Zumba\CQRS\Command\Command,
	\Zumba\CQRS\Command\Response,
	\Zumba\Util\Log;

class Logger implements \Zumba\CQRS\Command\Middleware {

	/**
	 * Log level to use for all logging.
	 *
	 * @var integer
	 */
	protected $level;

	/**
	 * Logger Middleware logs all commands
	 */
	protected function __construct(int $level) {
		$this->level = $level;
	}

	/**
	 * Create a Logger middleware from a particular Log Level
	 */
	public static function fromLevel(int $level = Log::LEVEL_INFO) : Logger {
		return new static($level);
	}

	/**
	 * Log the command
	 */
	public function handle(Command $command, callable $next) : Response {
		Log::write(sprintf('Command dispatched to handlers: %s', get_class($command)), $this->level, 'command');
		return $next($command);
	}
}
