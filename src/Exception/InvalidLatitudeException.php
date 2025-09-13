<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Exception;

use InvalidArgumentException;

/**
 * Exception thrown when an invalid latitude value is provided.
 *
 * @since 0.0.1
 */
final class InvalidLatitudeException extends InvalidArgumentException
{
    /**
     * Create an exception for latitude values outside the valid range.
     *
     * @param float $value The invalid latitude value
     *
     * @example
     * throw InvalidLatitudeException::outOfRange(91.0);
     * // Throws: "Latitude must be between -90 and 90 degrees, 91.0 given"
     */
    public static function outOfRange(float $value): self
    {
        return new self(
            sprintf('Latitude must be between -90 and 90 degrees, %f given', $value)
        );
    }

    /**
     * Create an exception for invalid latitude string values.
     *
     * @param string $value The invalid latitude string
     *
     * @example
     * throw InvalidLatitudeException::invalidString('invalid');
     * // Throws: "Invalid latitude value: invalid"
     */
    public static function invalidString(string $value): self
    {
        return new self(
            sprintf('Invalid latitude value: %s', $value)
        );
    }
}
