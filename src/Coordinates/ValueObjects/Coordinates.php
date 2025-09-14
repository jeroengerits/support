<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use Stringable;

/**
 * Value object representing geographic coordinates.
 *
 * This immutable value object represents a point on Earth using latitude
 * and longitude values. It provides methods for distance calculations,
 * equality comparison, and string representation.
 *
 * @package JeroenGerits\Support\Coordinates\ValueObjects
 * @since   1.0.0
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
 *
 * // Create coordinates using helper function
 * $nyc = coordinates(40.7128, -74.0060);
 *
 * // Calculate distance to another coordinate
 * $london = coordinates(51.5074, -0.1278);
 * $distance = $nyc->distanceBetween($london, DistanceUnit::KILOMETERS);
 *
 * // String representation
 * echo $nyc; // "40.7128,-74.0060"
 *
 * // Equality comparison
 * $same = coordinates(40.7128, -74.0060);
 * $isEqual = $nyc->isEqual($same); // true
 * ```
 */
class Coordinates implements Equatable, Stringable
{
    /**
     * Create a new Coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value object
     * @param Longitude $longitude The longitude value object
     *
     * @example
     * ```php
     * // Create from value objects using helper functions
     * $lat = latitude(40.7128);
     * $lng = longitude(-74.0060);
     * $coord = new Coordinates($lat, $lng);
     *
     * // Using constructor promotion with helper functions
     * $coord = new Coordinates(
     *     latitude: latitude(51.5074),
     *     longitude: longitude(-0.1278)
     * );
     *
     * // Or simply use the coordinates helper function
     * $coord = coordinates(51.5074, -0.1278);
     * ```
     */
    public function __construct(public Latitude $latitude, public Longitude $longitude) {}

    /**
     * Get the string representation of the coordinates.
     *
     * @return string The coordinates as "latitude,longitude"
     *
     * @example
     * ```php
     * $coord = coordinates(40.7128, -74.0060);
     * echo $coord; // "40.7128,-74.0060"
     *
     * // Can be used in string concatenation
     * $message = "Location: " . $coord; // "Location: 40.7128,-74.0060"
     * ```
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    /**
     * Check if this coordinates object is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the coordinates are equal
     *
     * @example
     * ```php
     * $coord1 = coordinates(40.7128, -74.0060);
     * $coord2 = coordinates(40.7128, -74.0060);
     * $coord3 = coordinates(51.5074, -0.1278);
     *
     * $isEqual = $coord1->isEqual($coord2); // true - same values
     * $isEqual = $coord1->isEqual($coord3); // false - different values
     *
     * // Different types are not equal
     * $lat = latitude(40.7128);
     * $isEqual = $coord1->isEqual($lat); // false - different types
     * ```
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->longitude->isEqual($other->longitude) && $this->latitude->isEqual($other->latitude);
    }

    /**
     * Calculate the distance between two coordinates.
     *
     * @param  mixed        $latitude  The latitude value or Coordinates object
     * @param  mixed        $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @param  DistanceUnit $unit      The unit of distance to return
     * @return float        The distance between the two coordinates
     *
     * @example
     * ```php
     * $nyc = coordinates(40.7128, -74.0060);
     *
     * // Calculate distance to another Coordinates object
     * $london = coordinates(51.5074, -0.1278);
     * $distance = $nyc->distanceBetween($london, DistanceUnit::KILOMETERS);
     * // Returns: ~5570.0 km
     *
     * // Calculate distance using individual values
     * $distance = $nyc->distanceBetween(51.5074, -0.1278, DistanceUnit::MILES);
     * // Returns: ~3458.0 miles
     *
     * // Using string values
     * $distance = $nyc->distanceBetween('48.8566', '2.3522', DistanceUnit::KILOMETERS);
     * // Returns: ~5837.0 km (NYC to Paris)
     * ```
     */
    public function distanceBetween(mixed $latitude, mixed $longitude = null, DistanceUnit $unit = DistanceUnit::KILOMETERS): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, $unit);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), $unit);
    }

    /**
     * Calculate the distance between two coordinates in miles.
     *
     * @param  mixed $latitude  The latitude value or Coordinates object
     * @param  mixed $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @return float The distance between the two coordinates in miles
     *
     * @example
     * ```php
     * $nyc = coordinates(40.7128, -74.0060);
     *
     * // Calculate distance in miles to another Coordinates object
     * $london = coordinates(51.5074, -0.1278);
     * $distance = $nyc->distanceBetweenInMiles($london);
     * // Returns: ~3458.0 miles
     *
     * // Calculate distance using individual values
     * $distance = $nyc->distanceBetweenInMiles(48.8566, 2.3522);
     * // Returns: ~3625.0 miles (NYC to Paris)
     * ```
     */
    public function distanceBetweenInMiles(mixed $latitude, mixed $longitude = null): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, DistanceUnit::MILES);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), DistanceUnit::MILES);
    }

    /**
     * Calculate the distance between two coordinates in kilometers.
     *
     * @param  mixed $latitude  The latitude value or Coordinates object
     * @param  mixed $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @return float The distance between the two coordinates in kilometers
     *
     * @example
     * ```php
     * $nyc = coordinates(40.7128, -74.0060);
     *
     * // Calculate distance in kilometers to another Coordinates object
     * $london = coordinates(51.5074, -0.1278);
     * $distance = $nyc->distanceBetweenInKilometers($london);
     * // Returns: ~5570.0 km
     *
     * // Calculate distance using individual values
     * $distance = $nyc->distanceBetweenInKilometers(48.8566, 2.3522);
     * // Returns: ~5837.0 km (NYC to Paris)
     * ```
     */
    public function distanceBetweenInKilometers(mixed $latitude, mixed $longitude = null): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, DistanceUnit::KILOMETERS);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), DistanceUnit::KILOMETERS);
    }
}
