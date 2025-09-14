<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Base exception class for coordinate-related errors.
 *
 * This abstract class provides a foundation for all coordinate-related
 * exceptions with standardized error codes and consistent error handling.
 * All coordinate exceptions should extend this class to maintain
 * consistent error reporting across the library.
 *
 * @package JeroenGerits\Support\Coordinates\Exceptions
 * @since   1.0.0
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\Exceptions\BaseCoordinatesException;
 *
 * // Custom coordinate exception
 * class CustomCoordinateException extends BaseCoordinatesException
 * {
 *     public function __construct(string $message = 'Custom coordinate error')
 *     {
 *         parent::__construct($message, self::CODE_INVALID_VALUE);
 *     }
 * }
 *
 * // Throw with custom message and code
 * throw new CustomCoordinateException('Invalid coordinate format', BaseCoordinatesException::CODE_INVALID_FORMAT);
 * ```
 */
abstract class BaseCoordinatesException extends Exception
{
    /**
     * Error code for invalid coordinate values.
     *
     * Used when coordinate values are malformed or cannot be processed.
     */
    public const int CODE_INVALID_VALUE = 1001;

    /**
     * Error code for coordinate values outside valid range.
     *
     * Used when latitude is outside -90.0 to +90.0 or longitude is outside -180.0 to +180.0.
     */
    public const int CODE_OUT_OF_RANGE = 1002;

    /**
     * Error code for invalid coordinate data types.
     *
     * Used when the provided value is not a valid type (e.g., array instead of float).
     */
    public const int CODE_INVALID_TYPE = 1003;

    /**
     * Error code for missing coordinate values.
     *
     * Used when required coordinate data is not provided in arrays or method calls.
     */
    public const int CODE_MISSING_VALUE = 1004;

    /**
     * Error code for invalid coordinate format.
     *
     * Used when coordinate strings or data structures are not in the expected format.
     */
    public const int CODE_INVALID_FORMAT = 1005;

    /**
     * Create a new BaseCoordinatesException instance.
     *
     * This constructor provides a consistent way to create coordinate-related
     * exceptions with standardized error codes and optional exception chaining.
     *
     * @param string         $message  The exception message describing the error
     * @param int            $code     The exception code (use class constants)
     * @param Exception|null $previous The previous exception for chaining
     *
     * @example
     * ```php
     * // Basic exception with default values
     * $exception = new CustomCoordinateException();
     *
     * // Exception with custom message
     * $exception = new CustomCoordinateException('Invalid latitude value provided');
     *
     * // Exception with custom message and code
     * $exception = new CustomCoordinateException(
     *     'Latitude out of range',
     *     BaseCoordinatesException::CODE_OUT_OF_RANGE
     * );
     *
     * // Exception with chaining
     * $previous = new InvalidArgumentException('Original error');
     * $exception = new CustomCoordinateException(
     *     'Coordinate validation failed',
     *     BaseCoordinatesException::CODE_INVALID_VALUE,
     *     $previous
     * );
     * ```
     */
    public function __construct(
        string $message = 'Invalid coordinate value provided',
        int $code = self::CODE_INVALID_VALUE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
