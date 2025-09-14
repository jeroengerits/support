<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Enums;

/**
 * Distance unit enumeration for coordinate calculations.
 *
 * This enum provides standardized distance units for coordinate-based
 * distance calculations, ensuring consistent units across the library.
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
 * use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
 *
 * $calculator = new CoordinatesCalculator();
 *
 * // Calculate distance in kilometers
 * $distanceKm = $calculator->distanceBetween($coord1, $coord2, DistanceUnit::KILOMETERS);
 *
 * // Calculate distance in miles
 * $distanceMi = $calculator->distanceBetween($coord1, $coord2, DistanceUnit::MILES);
 *
 * // Get the string value
 * $unitString = DistanceUnit::KILOMETERS->value; // 'km'
 * ```
 */
enum DistanceUnit: string
{
    case KILOMETERS = 'km';
    case MILES = 'mi';
}
