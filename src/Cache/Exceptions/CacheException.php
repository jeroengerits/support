<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Exceptions;

use Exception;

/**
 * Base exception class for cache-related errors.
 */
abstract class CacheException extends Exception
{
    /** Error code for invalid cache key values. */
    public const int CODE_INVALID_KEY = 2001;

    /** Error code for invalid time-to-live values. */
    public const int CODE_INVALID_TTL = 2002;

    /** Error code for cache operation failures. */
    public const int CODE_OPERATION_FAILED = 2003;

    /** Error code for cache adapter errors. */
    public const int CODE_ADAPTER_ERROR = 2004;

    /** Error code for cache configuration errors. */
    public const int CODE_CONFIGURATION_ERROR = 2005;

    /**
     * @param string         $message  The exception message describing the error
     * @param int            $code     The exception code (use class constants)
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message = 'Cache operation failed',
        int $code = self::CODE_OPERATION_FAILED,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
