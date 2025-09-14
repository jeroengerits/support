<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

if (! function_exists('coordinates')) {
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * This helper function provides a convenient way to create coordinate objects
     * from various input types including arrays, individual values, or existing
     * coordinate objects.
     *
     * @param  mixed       $latitude  The latitude value or array containing both coordinates
     * @param  mixed       $longitude The longitude value (optional when $latitude is an array)
     * @return Coordinates A new Coordinates instance
     *
     * @throws InvalidCoordinatesException
     *
     * @example
     * ```php
     * // Create from individual values
     * $coord1 = coordinates(40.7128, -74.0060); // New York
     *
     * // Create from string values
     * $coord2 = coordinates('51.5074', '-0.1278'); // London
     *
     * // Create from array with named keys
     * $coord3 = coordinates(['lat' => 48.8566, 'lng' => 2.3522]); // Paris
     *
     * // Create from array with numeric keys
     * $coord4 = coordinates([35.6762, 139.6503]); // Tokyo
     *
     * // Create from existing coordinate objects
     * $lat = latitude(40.7128);
     * $lng = longitude(-74.0060);
     * $coord5 = coordinates($lat, $lng);
     * ```
     */
    function coordinates(mixed $latitude = null, mixed $longitude = null): Coordinates
    {
        return CoordinatesFactory::createCoordinates($latitude, $longitude);
    }
}

if (! function_exists('latitude')) {
    /**
     * Create a new Latitude instance from various input types.
     *
     * This helper function provides a convenient way to create latitude objects
     * from various input types with automatic validation and type conversion.
     *
     * @param  mixed    $value The latitude value (-90.0 to +90.0 degrees)
     * @return Latitude A new Latitude instance
     *
     * @throws InvalidCoordinatesException When latitude value is invalid or out of range
     *
     * @example
     * ```php
     * // Create from float value
     * $lat1 = latitude(40.7128); // New York latitude
     *
     * // Create from string value
     * $lat2 = latitude('51.5074'); // London latitude
     *
     * // Create from integer value
     * $lat3 = latitude(48); // Will be converted to 48.0
     *
     * // Create from existing Latitude object (returns same instance)
     * $existingLat = new Latitude(40.7128);
     * $lat4 = latitude($existingLat); // Returns the same object
     * ```
     */
    function latitude(mixed $value = null): Latitude
    {
        return CoordinatesFactory::createLatitude($value);
    }
}

if (! function_exists('longitude')) {
    /**
     * Create a new Longitude instance from various input types.
     *
     * This helper function provides a convenient way to create longitude objects
     * from various input types with automatic validation and type conversion.
     *
     * @param  mixed     $value The longitude value (-180.0 to +180.0 degrees)
     * @return Longitude A new Longitude instance
     *
     * @throws InvalidCoordinatesException When longitude value is invalid or out of range
     *
     * @example
     * ```php
     * // Create from float value
     * $lng1 = longitude(-74.0060); // New York longitude
     *
     * // Create from string value
     * $lng2 = longitude('-0.1278'); // London longitude
     *
     * // Create from integer value
     * $lng3 = longitude(2); // Will be converted to 2.0
     *
     * // Create from existing Longitude object (returns same instance)
     * $existingLng = new Longitude(-74.0060);
     * $lng4 = longitude($existingLng); // Returns the same object
     * ```
     */
    function longitude(mixed $value = null): Longitude
    {
        return CoordinatesFactory::createLongitude($value);
    }
}
