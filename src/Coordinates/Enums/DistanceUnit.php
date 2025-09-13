<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Enums;

/**
 * Enumeration of supported distance units for coordinate calculations.
 *
 * @since 0.0.1
 */
enum DistanceUnit: string
{
    case KILOMETERS = 'km';
    case MILES = 'mi';
    case NAUTICAL_MILES = 'nmi';
    case METERS = 'm';
    case MILLIMETERS = 'mm';
    case CENTIMETERS = 'cm';
    case DECIMETERS = 'dm';
    case INCHES = 'in';
    case FEET = 'ft';
    case YARDS = 'yd';
    case LIGHT_YEARS = 'ly';

    /**
     * Get the conversion factor from kilometers to this unit.
     *
     * @return float The conversion factor
     *
     * @example
     * $factor = DistanceUnit::MILES->getConversionFactor();
     * // Returns: 0.621371 (km to miles)
     */
    public function getConversionFactor(): float
    {
        return match ($this) {
            self::KILOMETERS => 1.0,
            self::MILES => 0.621371,
            self::NAUTICAL_MILES => 0.539957,
            self::METERS => 1000.0,
            self::MILLIMETERS => 1000000.0,
            self::CENTIMETERS => 100000.0,
            self::DECIMETERS => 10000.0,
            self::INCHES => 39370.1,
            self::FEET => 3280.84,
            self::YARDS => 1093.61,
            self::LIGHT_YEARS => 1.057e-13,
        };
    }

    /**
     * Get the display name for this distance unit.
     *
     * @return string The display name
     *
     * @example
     * $name = DistanceUnit::MILES->getDisplayName();
     * // Returns: "miles"
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::KILOMETERS => 'kilometers',
            self::MILES => 'miles',
            self::NAUTICAL_MILES => 'nautical miles',
            self::METERS => 'meters',
            self::MILLIMETERS => 'millimeters',
            self::CENTIMETERS => 'centimeters',
            self::DECIMETERS => 'decimeters',
            self::INCHES => 'inches',
            self::FEET => 'feet',
            self::YARDS => 'yards',
            self::LIGHT_YEARS => 'light years',
        };
    }

    /**
     * Get the abbreviation for this distance unit.
     *
     * @return string The abbreviation
     *
     * @example
     * $abbr = DistanceUnit::MILES->getAbbreviation();
     * // Returns: "mi"
     */
    public function getAbbreviation(): string
    {
        return $this->value;
    }
}
