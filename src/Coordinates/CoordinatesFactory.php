<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use InvalidArgumentException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

class CoordinatesFactory
{
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float|int|string|array|Latitude|null $latitude  The latitude value or array containing both coordinates
     * @param float|int|string|Longitude|null      $longitude The longitude value (optional when $latitude is an array)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When latitude or longitude values are invalid
     */
    public static function createCoordinates(float|int|string|array|Latitude|null $latitude = null, float|int|string|Longitude|null $longitude = null): Coordinates
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
     * @param array $array The array containing coordinate data
     *
     * @throws InvalidCoordinatesException When coordinate values are missing or malformed
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
     * @param float|int|string|Latitude|null $value The latitude value (float, string, int, or Latitude instance)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When latitude value is invalid
     */
    public static function createLatitude(float|int|string|Latitude|null $value): Latitude
    {
        return match (true) {
            $value instanceof Latitude => $value,
            is_string($value) => new Latitude((float) $value),
            is_float($value) => new Latitude($value),
            is_int($value) => new Latitude((float) $value),
            $value === null => throw InvalidCoordinatesException::missingFromArray([], 'latitude'),
            default => throw InvalidCoordinatesException::invalidType($value, 'latitude'),
        };
    }

    /**
     * Create a new Longitude instance from various input types.
     *
     * @param float|int|string|Longitude|null $value The longitude value (float, string, int, or Longitude instance)
     *
     * @throws InvalidArgumentException|InvalidCoordinatesException When longitude value is invalid
     */
    public static function createLongitude(float|int|string|Longitude|null $value): Longitude
    {
        return match (true) {
            $value instanceof Longitude => $value,
            is_string($value) => new Longitude((float) $value),
            is_float($value) => new Longitude($value),
            is_int($value) => new Longitude((float) $value),
            $value === null => throw InvalidCoordinatesException::missingFromArray([], 'longitude'),
            default => throw InvalidCoordinatesException::invalidType($value, 'longitude'),
        };
    }
}
