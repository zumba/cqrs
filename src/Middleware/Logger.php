<?php

declare(strict_types=1);

namespace Zumba\CQRS\Middleware;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;

class Logger implements Middleware
{
    use LoggerAwareTrait;

    /**
     * Log level to use for all logging.
     *
     * @var string
     */
    protected $level;

    /**
     * Logger Middleware logs all commands
     */
    protected function __construct(string $level)
    {
        $this->level = $level;
    }

    /**
     * Create a Logger middleware from a particular Log Level
     */
    public static function fromLevel(string $level = LogLevel::INFO): Logger
    {
        return new static($level);
    }

    /**
     * Log the DTO
     */
    public function handle(DTO $dto, callable $next): Response
    {
        $this->logger?->{$this->level}(sprintf('DTO dispatched to handlers: %s', get_class($dto)));
        return $next($dto);
    }
}
