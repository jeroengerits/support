<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid longitude value is provided.
 *
 * This exception is thrown when a longitude value is outside the valid range
 * of -180.0 to +180.0 degrees, or when an invalid type is provided.
 */
class InvalidLongitudeException extends Exception
{
    /**
     * Create a new InvalidLongitudeException instance.
     *
     * @param string $message The exception message
     * @param int    $code    The exception code
     */
    public function __construct(string $message = 'Invalid longitude value provided', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
