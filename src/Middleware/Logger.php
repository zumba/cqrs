<?php

declare(strict_types=1);

namespace Zumba\CQRS\Middleware;

use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;
use Zumba\Util\Log;

class Logger implements Middleware
{
    /**
     * Log level to use for all logging.
     *
     * @var integer
     */
    protected $level;

    /**
     * Logger Middleware logs all commands
     */
    protected function __construct(int $level)
    {
        $this->level = $level;
    }

    /**
     * Create a Logger middleware from a particular Log Level
     */
    public static function fromLevel(int $level = Log::LEVEL_INFO): Logger
    {
        return new static($level);
    }

    /**
     * Log the DTO
     */
    public function handle(DTO $dto, callable $next): Response
    {
        Log::write(sprintf('DTO dispatched to handlers: %s', get_class($dto)), $this->level, 'cqrs');
        return $next($dto);
    }
}
