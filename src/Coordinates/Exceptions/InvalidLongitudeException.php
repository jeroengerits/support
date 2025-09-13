<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when an invalid longitude value is provided.
 *
 * @since 0.0.1
 */
final class InvalidLongitudeException extends InvalidArgumentException
{
    /**
     * Create an exception for longitude values outside the valid range.
     *
     * @param float $value The invalid longitude value
     *
     * @example
     * throw InvalidLongitudeException::outOfRange(181.0);
     * // Throws: "Longitude must be between -180 and 180 degrees, 181.0 given"
     */
    public static function outOfRange(float $value): self
    {
        return new self(
            sprintf('Longitude must be between -180 and 180 degrees, %f given', $value)
        );
    }

    /**
     * Create an exception for invalid longitude string values.
     *
     * @param string $value The invalid longitude string
     *
     * @example
     * throw InvalidLongitudeException::invalidString('invalid');
     * // Throws: "Invalid longitude value: invalid"
     */
    public static function invalidString(string $value): self
    {
        return new self(
            sprintf('Invalid longitude value: %s', $value)
        );
    }
}
