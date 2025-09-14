<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Base exception class for coordinate-related errors.
 */
abstract class BaseCoordinatesException extends Exception
{
    /**
     * Error codes for different types of coordinate exceptions.
     */
    public const int CODE_INVALID_VALUE = 1001;

    public const int CODE_OUT_OF_RANGE = 1002;

    public const int CODE_INVALID_TYPE = 1003;

    public const int CODE_MISSING_VALUE = 1004;

    public const int CODE_INVALID_FORMAT = 1005;

    /**
     * Create a new BaseCoordinatesException instance.
     */
    public function __construct(
        string $message = 'Invalid coordinate value provided',
        int $code = self::CODE_INVALID_VALUE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
