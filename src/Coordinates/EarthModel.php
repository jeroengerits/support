<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

/**
 * Enum representing different Earth models for distance calculations.
 */
enum EarthModel: string
{
    /** Spherical Earth model with mean radius. */
    case SPHERICAL = 'spherical';

    /** WGS84 Earth model (World Geodetic System 1984). */
    case WGS84 = 'wgs84';

    /** GRS80 Earth model (Geodetic Reference System 1980). */
    case GRS80 = 'grs80';

    /**
     * Get the Earth radius for the specified distance unit.
     *
     * @param  DistanceUnit $unit The distance unit
     * @return float        The Earth radius in the specified unit
     */
    public function getRadius(DistanceUnit $unit): float
    {
        return match ($unit) {
            DistanceUnit::KILOMETERS => $this->getRadiusKm(),
            DistanceUnit::MILES => $this->getRadiusMiles(),
        };
    }

    /**
     * Get the Earth radius in kilometers for this model.
     *
     * @return float The Earth radius in kilometers
     */
    public function getRadiusKm(): float
    {
        return match ($this) {
            self::SPHERICAL => 6371.0,
            self::WGS84 => 6371.0088, // Mean radius
            self::GRS80 => 6371.0000, // Mean radius
        };
    }

    /**
     * Get the Earth radius in miles for this model.
     *
     * @return float The Earth radius in miles
     */
    public function getRadiusMiles(): float
    {
        return match ($this) {
            self::SPHERICAL => 3958.8,
            self::WGS84 => 3958.7613,
            self::GRS80 => 3958.7600,
        };
    }
}
