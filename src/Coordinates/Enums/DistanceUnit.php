<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Enums;

/**
 * Distance unit enumeration for coordinate calculations.
 *
 * This enum provides standardized distance units for coordinate-based
 * distance calculations, ensuring consistent units across the library.
 *
 * @package JeroenGerits\Support\Coordinates\Enums
 * @since   1.0.0
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
 *
 * $nyc = coordinates(40.7128, -74.0060);
 * $london = coordinates(51.5074, -0.1278);
 *
 * // Calculate distance in kilometers
 * $distanceKm = $nyc->distanceBetween($london, DistanceUnit::KILOMETERS);
 *
 * // Calculate distance in miles
 * $distanceMi = $nyc->distanceBetween($london, DistanceUnit::MILES);
 *
 * // Get the string value
 * $unitString = DistanceUnit::KILOMETERS->value; // 'km'
 * ```
 */
enum DistanceUnit: string
{
    /**
     * Kilometers unit for distance calculations.
     *
     * @var string
     *
     * @example
     * ```php
     * $nyc = coordinates(40.7128, -74.0060);
     * $london = coordinates(51.5074, -0.1278);
     * $distance = $nyc->distanceBetween($london, DistanceUnit::KILOMETERS);
     * ```
     */
    case KILOMETERS = 'km';

    /**
     * Miles unit for distance calculations.
     *
     * @var string
     *
     * @example
     * ```php
     * $nyc = coordinates(40.7128, -74.0060);
     * $london = coordinates(51.5074, -0.1278);
     * $distance = $nyc->distanceBetween($london, DistanceUnit::MILES);
     * ```
     */
    case MILES = 'mi';
}
