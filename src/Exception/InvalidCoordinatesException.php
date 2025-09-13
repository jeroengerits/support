<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Exception;

/**
 * Exception thrown when invalid coordinates are provided.
 *
 * @since 0.0.1
 */
final class InvalidCoordinatesException extends SupportException
{
    /**
     * Create an exception for missing array keys.
     *
     *
     * @example
     * throw InvalidCoordinatesException::missingArrayKeys();
     * // Throws: "Array must contain both latitude and longitude keys"
     */
    public static function missingArrayKeys(): self
    {
        return new self(
            'Array must contain both latitude and longitude keys'
        );
    }

    /**
     * Create an exception for invalid string format.
     *
     *
     * @example
     * throw InvalidCoordinatesException::invalidStringFormat();
     * // Throws: "Invalid coordinates format. Expected "latitude,longitude""
     */
    public static function invalidStringFormat(): self
    {
        return new self(
            'Invalid coordinates format. Expected "latitude,longitude"'
        );
    }
}
