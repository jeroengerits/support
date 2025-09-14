<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

class InvalidCoordinatesException extends Exception
{
    /**
     * Create a new InvalidLatitudeException instance.
     *
     * @param string $message The exception message
     * @param int    $code    The exception code
     */
    public function __construct(string $message = 'Invalid coordinates values provided', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
