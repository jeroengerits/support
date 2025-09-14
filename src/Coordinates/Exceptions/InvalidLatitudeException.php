<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid latitude value is provided.
 *
 * This exception is thrown when a latitude value is outside the valid range
 * of -90.0 to +90.0 degrees, or when an invalid type is provided.
 */
class InvalidLatitudeException extends Exception
{
    /**
     * Create a new InvalidLatitudeException instance.
     *
     * @param string $message The exception message
     * @param int    $code    The exception code
     */
    public function __construct(string $message = 'Invalid latitude value provided', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
