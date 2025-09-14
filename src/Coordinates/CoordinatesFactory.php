<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use InvalidArgumentException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

/**
 * Factory class for creating coordinate-related objects.
 *
 * This factory provides convenient methods for creating Coordinates, Latitude,
 * and Longitude objects from various input types with automatic validation
 * and type conversion.
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\CoordinatesFactory;
 *
 * // Create coordinates from individual values
 * $coord1 = CoordinatesFactory::createCoordinates(40.7128, -74.0060);
 *
 * // Create coordinates from array
 * $coord2 = CoordinatesFactory::createFromArray(['lat' => 51.5074, 'lng' => -0.1278]);
 *
 * // Create latitude and longitude separately
 * $lat = CoordinatesFactory::createLatitude(48.8566);
 * $lng = CoordinatesFactory::createLongitude(2.3522);
 * ```
 */
class CoordinatesFactory
{
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * This method provides flexible input handling for creating coordinate
     * objects from various data types including arrays, individual values,
     * or existing coordinate objects.
     *
     * @param mixed $latitude  The latitude value or array containing both coordinates
     * @param mixed $longitude The longitude value (optional when $latitude is an array)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When latitude or longitude values are invalid
     *
     * @example
     * ```php
     * // Create from individual float values
     * $coord1 = CoordinatesFactory::createCoordinates(40.7128, -74.0060);
     *
     * // Create from string values
     * $coord2 = CoordinatesFactory::createCoordinates('51.5074', '-0.1278');
     *
     * // Create from array (longitude parameter ignored)
     * $coord3 = CoordinatesFactory::createCoordinates(['lat' => 48.8566, 'lng' => 2.3522]);
     *
     * // Create from existing coordinate objects
     * $lat = new Latitude(35.6762);
     * $lng = new Longitude(139.6503);
     * $coord4 = CoordinatesFactory::createCoordinates($lat, $lng);
     * ```
     */
    public static function createCoordinates(mixed $latitude = null, mixed $longitude = null): Coordinates
    {
        // Handle array input for latitude
        if (is_array($latitude)) {
            return self::createFromArray($latitude);
        }

        return new Coordinates(
            latitude: self::createLatitude($latitude),
            longitude: self::createLongitude($longitude)
        );
    }

    /**
     * Create a new Coordinates instance from an array.
     *
     * This method supports multiple array formats including named keys
     * (lat/lng, latitude/longitude) and numeric indexed arrays.
     *
     * @param array $array The array containing coordinate data
     *
     * @throws InvalidCoordinatesException When coordinate values are missing or malformed
     *
     * @example
     * ```php
     * // Array with named keys (lat/lng)
     * $coord1 = CoordinatesFactory::createFromArray(['lat' => 40.7128, 'lng' => -74.0060]);
     *
     * // Array with named keys (latitude/longitude)
     * $coord2 = CoordinatesFactory::createFromArray(['latitude' => 51.5074, 'longitude' => -0.1278]);
     *
     * // Numeric indexed array
     * $coord3 = CoordinatesFactory::createFromArray([48.8566, 2.3522]); // [lat, lng]
     *
     * // Mixed string/numeric values
     * $coord4 = CoordinatesFactory::createFromArray(['lat' => '35.6762', 'lng' => '139.6503']);
     * ```
     */
    public static function createFromArray(array $array): Coordinates
    {
        $lat = $array['lat'] ?? $array['latitude'] ?? $array[0] ?? null;
        $lng = $array['lng'] ?? $array['longitude'] ?? $array[1] ?? null;

        if ($lat === null && $lng === null) {
            throw InvalidCoordinatesException::invalidArrayStructure($array);
        } elseif ($lat === null) {
            throw InvalidCoordinatesException::missingFromArray($array, 'latitude');
        } elseif ($lng === null) {
            throw InvalidCoordinatesException::missingFromArray($array, 'longitude');
        }

        return new Coordinates(
            latitude: self::createLatitude($lat),
            longitude: self::createLongitude($lng)
        );
    }

    /**
     * Create a new Latitude instance from various input types.
     *
     * This method handles automatic type conversion and validation for
     * latitude values, ensuring they fall within the valid range of
     * -90.0 to +90.0 degrees.
     *
     * @param mixed $value The latitude value (float, string, int, or Latitude instance)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When latitude value is invalid
     *
     * @example
     * ```php
     * // Create from float value
     * $lat1 = CoordinatesFactory::createLatitude(40.7128);
     *
     * // Create from string value
     * $lat2 = CoordinatesFactory::createLatitude('51.5074');
     *
     * // Create from integer value
     * $lat3 = CoordinatesFactory::createLatitude(48); // Converts to 48.0
     *
     * // Create from existing Latitude object (returns same instance)
     * $existingLat = new Latitude(35.6762);
     * $lat4 = CoordinatesFactory::createLatitude($existingLat); // Returns same object
     *
     * // Invalid values will throw exceptions
     * // $lat5 = CoordinatesFactory::createLatitude(95.0); // Throws InvalidCoordinatesException
     * ```
     */
    public static function createLatitude(mixed $value): Latitude
    {
        return match (true) {
            $value instanceof Latitude => $value,
            is_string($value), is_int($value) => new Latitude((float) $value),
            is_float($value) => new Latitude($value),
            $value === null => throw InvalidCoordinatesException::invalidType(null, 'latitude'),
            default => throw InvalidCoordinatesException::invalidType($value, 'latitude'),
        };
    }

    /**
     * Create a new Longitude instance from various input types.
     *
     * This method handles automatic type conversion and validation for
     * longitude values, ensuring they fall within the valid range of
     * -180.0 to +180.0 degrees.
     *
     * @param mixed $value The longitude value (float, string, int, or Longitude instance)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When longitude value is invalid
     *
     * @example
     * ```php
     * // Create from float value
     * $lng1 = CoordinatesFactory::createLongitude(-74.0060);
     *
     * // Create from string value
     * $lng2 = CoordinatesFactory::createLongitude('-0.1278');
     *
     * // Create from integer value
     * $lng3 = CoordinatesFactory::createLongitude(2); // Converts to 2.0
     *
     * // Create from existing Longitude object (returns same instance)
     * $existingLng = new Longitude(139.6503);
     * $lng4 = CoordinatesFactory::createLongitude($existingLng); // Returns same object
     *
     * // Invalid values will throw exceptions
     * // $lng5 = CoordinatesFactory::createLongitude(185.0); // Throws InvalidCoordinatesException
     * ```
     */
    public static function createLongitude(mixed $value): Longitude
    {
        return match (true) {
            $value instanceof Longitude => $value,
            is_string($value), is_int($value) => new Longitude((float) $value),
            is_float($value) => new Longitude($value),
            $value === null => throw InvalidCoordinatesException::invalidType(null, 'longitude'),
            default => throw InvalidCoordinatesException::invalidType($value, 'longitude'),
        };
    }
}
