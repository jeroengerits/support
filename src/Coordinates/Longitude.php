<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

/**
 * Value object representing a longitude coordinate.
 *
 * Longitude values range from -180.0° (International Date Line West) to +180.0° (International Date Line East).
 * This class provides immutable longitude objects with automatic validation.
 *
 * @example
 * ```php
 * $longitude = Longitude::create(-74.0060); // New York longitude
 * echo $longitude; // "-74.0060"
 * ```
 */
class Longitude extends AbstractCoordinate
{
    /** Minimum valid longitude value in decimal degrees (International Date Line West). */
    public const float MIN_LONGITUDE = -180.0;

    /** Maximum valid longitude value in decimal degrees (International Date Line East). */
    public const float MAX_LONGITUDE = 180.0;

    /**
     * Validate the longitude value.
     *
     * @param float $value The longitude value to validate
     *
     * @throws InvalidCoordinatesException When longitude value is outside valid range
     */
    protected function validateValue(float $value): void
    {
        if ($value < self::MIN_LONGITUDE || $value > self::MAX_LONGITUDE) {
            throw $this->createOutOfRangeException($value);
        }
    }

    /**
     * Get the minimum valid value for longitude.
     *
     * @return float The minimum valid longitude value (-180.0)
     */
    protected function getMinValue(): float
    {
        return self::MIN_LONGITUDE;
    }

    /**
     * Get the maximum valid value for longitude.
     *
     * @return float The maximum valid longitude value (180.0)
     */
    protected function getMaxValue(): float
    {
        return self::MAX_LONGITUDE;
    }

    /**
     * Get the name of this coordinate type for error messages.
     *
     * @return string The coordinate type name ("Longitude")
     */
    protected function getCoordinateTypeName(): string
    {
        return 'Longitude';
    }
}
