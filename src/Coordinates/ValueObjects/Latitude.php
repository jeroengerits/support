<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing a latitude coordinate.
 *
 * This immutable value object represents a latitude value in decimal degrees,
 * with automatic validation to ensure the value is within the valid range
 * of -90.0 to +90.0 degrees.
 *
 * @package JeroenGerits\Support\Coordinates\ValueObjects
 * @since   1.0.0
 *
 * @example
 * ```php
 * // Create valid latitude values using helper function
 * latitude(40.7128);
 *
 * // Equality comparison
 * latitude(40.7128)->isEqual(latitude(40.7128)); // true
 *
 * // Invalid values throw exceptions
 * latitude(95.0); // Throws InvalidCoordinatesException
 * ```
 */
class Latitude implements Equatable, Stringable
{
    /**
     * Create a new Latitude instance.
     *
     * @param float $value The latitude value in decimal degrees (-90.0 to +90.0)
     *
     * @throws InvalidCoordinatesException When latitude value is outside valid range
     *
     * @example
     * ```php
     * // Valid latitude values using helper function
     * latitude(40.7128);    // New York
     *
     * // Invalid values will throw exceptions
     * latitude(95.0);  // Too high
     * latitude(-95.0); // Too low
     * ```
     */
    public function __construct(public float $value)
    {
        if ($value < -90.0 || $value > 90.0) {
            throw InvalidCoordinatesException::latitudeOutOfRange($value);
        }
    }

    /**
     * Get the string representation of the latitude.
     *
     * @return string The latitude as a string
     *
     * @example
     * ```php
     * latitude(40.7128);
     * ```
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert the latitude value to a string.
     *
     * @return string The latitude value as a string
     *
     * @example
     * ```php
     * latitude(40.7128)->toString();
     * ```
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check if this latitude is equal to another.
     *
     * @param Equatable $other The other object to compare
     *
     * @return bool True if the latitudes are equal
     *
     * @example
     * ```php
     * latitude(40.7128)->isEqual(latitude(40.7128)); // true
     * latitude(40.7128)->isEqual(latitude(51.5074)); // false
     * latitude(40.7128)->isEqual(longitude(-74.0060)); // false
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
