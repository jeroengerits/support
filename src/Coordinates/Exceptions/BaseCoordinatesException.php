<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Base exception class for coordinate-related errors.
 */
abstract class BaseCoordinatesException extends Exception
{
    /** Error code for invalid coordinate values. */
    public const int CODE_INVALID_VALUE = 1001;

    /** Error code for coordinate values outside valid range. */
    public const int CODE_OUT_OF_RANGE = 1002;

    /** Error code for invalid coordinate data types. */
    public const int CODE_INVALID_TYPE = 1003;

    /** Error code for missing coordinate values. */
    public const int CODE_MISSING_VALUE = 1004;

    /** Error code for invalid coordinate format. */
    public const int CODE_INVALID_FORMAT = 1005;

    /**
     * @param string         $message  The exception message describing the error
     * @param int            $code     The exception code (use class constants)
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message = 'Invalid coordinate value provided',
        int $code = self::CODE_INVALID_VALUE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
