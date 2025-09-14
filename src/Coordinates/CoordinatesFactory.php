<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use InvalidArgumentException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

class CoordinatesFactory
{
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float|int|string|Latitude  $latitude  The latitude value (float, string, int, or Latitude instance)
     * @param float|int|string|Longitude $longitude The longitude value (float, string, int, or Longitude instance)
     *
     * @throws InvalidArgumentException|InvalidLatitudeException|InvalidLongitudeException When latitude or longitude values are invalid
     */
    public static function createCoordinates(float|int|string|Latitude $latitude, float|int|string|Longitude $longitude): Coordinates
    {
        return new Coordinates(
            latitude: CoordinatesFactory::createLatitude($latitude),
            longitude: CoordinatesFactory::createLongitude($longitude)
        );
    }

    /**
     * Create a new Latitude instance from various input types.
     *
     * @param float|int|string|Latitude $value The latitude value (float, string, int, or Latitude instance)
     *
     * @throws InvalidArgumentException|InvalidLatitudeException When latitude value is invalid
     */
    public static function createLatitude(float|int|string|Latitude $value): Latitude
    {
        return match (true) {
            $value instanceof Latitude => $value,
            is_string($value) => new Latitude((float) $value),
            is_float($value) => new Latitude($value),
            is_int($value) => new Latitude((float) $value),
            default => throw new InvalidArgumentException('Invalid latitude value'),
        };
    }

    /**
     * Create a new Longitude instance from various input types.
     *
     * @param float|int|string|Longitude $value The longitude value (float, string, int, or Longitude instance)
     *
     * @throws InvalidArgumentException|InvalidLongitudeException When longitude value is invalid
     */
    public static function createLongitude(float|int|string|Longitude $value): Longitude
    {
        return match (true) {
            $value instanceof Longitude => $value,
            is_string($value) => new Longitude((float) $value),
            is_float($value) => new Longitude($value),
            is_int($value) => new Longitude((float) $value),
            default => throw new InvalidArgumentException('Invalid longitude value'),
        };
    }
}
