<?php

declare(strict_types=1);

namespace Zumba\CQRS\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Zumba\CQRS\DTO;
use Zumba\CQRS\Middleware;
use Zumba\CQRS\Response;

final class Logger implements Middleware
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Log level to use for all logging.
     *
     * @var string
     */
    protected $level;

    /**
     * Logger Middleware logs all commands
     */
    protected function __construct(LoggerInterface $logger, string $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * Create a Logger middleware from a particular Log Level
     */
    public static function fromLoggerAndLevel(LoggerInterface $logger, string $level = LogLevel::INFO): Logger
    {
        return new static($logger, $level);
    }

    /**
     * Log the DTO
     */
    public function handle(DTO $dto, callable $next): Response
    {
        $this->logger->{$this->level}(sprintf('DTO dispatched to handlers: %s', get_class($dto)));
        return $next($dto);
    }
}
