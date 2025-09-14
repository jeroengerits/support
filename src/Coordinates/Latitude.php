<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

/**
 * Value object representing a latitude coordinate.
 *
 * Latitude values range from -90.0° (South Pole) to +90.0° (North Pole).
 * This class provides immutable latitude objects with automatic validation.
 *
 * @example
 * ```php
 * $latitude = Latitude::create(40.7128); // New York latitude
 * echo $latitude; // "40.7128"
 * ```
 */
class Latitude extends AbstractCoordinate
{
    /** Minimum valid latitude value in decimal degrees (South Pole). */
    public const float MIN_LATITUDE = -90.0;

    /** Maximum valid latitude value in decimal degrees (North Pole). */
    public const float MAX_LATITUDE = 90.0;

    /**
     * Validate the latitude value.
     *
     * @param float $value The latitude value to validate
     *
     * @throws InvalidCoordinatesException When latitude value is outside valid range
     */
    protected function validateValue(float $value): void
    {
        if ($value < self::MIN_LATITUDE || $value > self::MAX_LATITUDE) {
            throw $this->createOutOfRangeException($value);
        }
    }

    /**
     * Get the minimum valid value for latitude.
     *
     * @return float The minimum valid latitude value (-90.0)
     */
    protected function getMinValue(): float
    {
        return self::MIN_LATITUDE;
    }

    /**
     * Get the maximum valid value for latitude.
     *
     * @return float The maximum valid latitude value (90.0)
     */
    protected function getMaxValue(): float
    {
        return self::MAX_LATITUDE;
    }

    /**
     * Get the name of this coordinate type for error messages.
     *
     * @return string The coordinate type name ("Latitude")
     */
    protected function getCoordinateTypeName(): string
    {
        return 'Latitude';
    }
}
