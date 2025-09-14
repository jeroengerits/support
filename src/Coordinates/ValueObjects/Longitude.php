<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing a longitude coordinate.
 *
 * This immutable value object represents a longitude value in decimal degrees,
 * with automatic validation to ensure the value is within the valid range
 * of -180.0 to +180.0 degrees.
 *
 * @package JeroenGerits\Support\Coordinates\ValueObjects
 * @since   1.0.0
 *
 * @example
 * ```php
 * // Create valid longitude values using helper function
 * longitude(-74.0060);   // New York
 *
 * // Equality comparison
 * longitude(-74.0060)->isEqual(longitude(-74.0060)); // true
 * longitude(-74.0060)->isEqual(longitude(139.6503)); // false
 *
 * // Invalid values throw exceptions
 * longitude(185.0); // Throws InvalidCoordinatesException
 * ```
 */
class Longitude implements Equatable, Stringable
{
    /**
     * Create a new Longitude instance.
     *
     * @param float $value The longitude value in decimal degrees (-180.0 to +180.0)
     *
     * @throws InvalidCoordinatesException When longitude value is outside valid range
     *
     * @example
     * ```php
     * // Valid longitude values using helper function
     * longitude(-74.0060);  // New York
     *
     * // Invalid values will throw exceptions
     * longitude(185.0);  // Too high
     * longitude(-185.0); // Too low
     * ```
     */
    public function __construct(public float $value)
    {
        if ($value < -180.0 || $value > 180.0) {
            throw InvalidCoordinatesException::longitudeOutOfRange($value);
        }
    }

    /**
     * Get the string representation of the longitude.
     *
     * @return string The longitude as a string
     *
     * @example
     * ```php
     * longitude(-74.0060)->toString();
     * ```
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert the longitude value to a string.
     *
     * @return string The longitude value as a string
     *
     * @example
     * ```php
     * echo longitude(-74.0060); // "-74.0060"
     * ```
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check if this longitude is equal to another.
     *
     * @param Equatable $other The other object to compare
     *
     * @return bool True if the longitudes are equal
     *
     * @example
     * ```php
     * longitude(-74.0060)->isEqual(longitude(-74.0060)); // true
     * longitude(-74.0060)->isEqual(longitude(139.6503)); // false
     * longitude(-74.0060)->isEqual(latitude(40.7128)); // false
     * ```
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }
}
