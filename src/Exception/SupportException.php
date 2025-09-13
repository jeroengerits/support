<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Exception;

/**
 * Base exception class for all Support package exceptions.
 *
 * @since 0.0.1
 */
abstract class SupportException extends \Exception
{
    /**
     * Create a new Support exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
