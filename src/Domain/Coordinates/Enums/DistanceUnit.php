<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Enums;

/**
 * Distance unit enumeration.
 *
 * Defines various distance units with their conversion factors
 * relative to kilometers.
 */
enum DistanceUnit: string
{
    /** Kilometers */
    case KILOMETERS = 'km';

    /** Miles */
    case MILES = 'mi';

    /** Meters */
    case METERS = 'm';

    /** Feet */
    case FEET = 'ft';

    /** Nautical Miles */
    case NAUTICAL_MILES = 'nmi';

    /**
     * Get the conversion factor relative to kilometers.
     *
     * @return float The conversion factor
     */
    public function conversionFactor(): float
    {
        return match ($this) {
            self::KILOMETERS => 1.0,
            self::MILES => 0.621371,
            self::METERS => 1000.0,
            self::FEET => 3280.84,
            self::NAUTICAL_MILES => 0.539957,
        };
    }

    /**
     * Get the display name of the distance unit.
     *
     * @return string The display name
     */
    public function displayName(): string
    {
        return match ($this) {
            self::KILOMETERS => 'Kilometers',
            self::MILES => 'Miles',
            self::METERS => 'Meters',
            self::FEET => 'Feet',
            self::NAUTICAL_MILES => 'Nautical Miles',
        };
    }

    /**
     * Get the abbreviation of the distance unit.
     *
     * @return string The abbreviation
     */
    public function abbreviation(): string
    {
        return $this->value;
    }
}
