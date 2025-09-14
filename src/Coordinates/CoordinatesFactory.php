<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

/**
 * Factory class for creating coordinate-related objects.
 */
class CoordinatesFactory
{
    /**
     * @param float $latitude  The latitude value in decimal degrees
     * @param float $longitude The longitude value in decimal degrees
     *
     * @throws InvalidCoordinatesException When latitude or longitude values are invalid
     */
    public static function createCoordinates(float $latitude, float $longitude): Coordinates
    {
        return new Coordinates(
            latitude: new Latitude($latitude),
            longitude: new Longitude($longitude)
        );
    }

    /**
     * @param float $value The latitude value in decimal degrees
     *
     * @throws InvalidCoordinatesException When latitude value is invalid
     */
    public static function createLatitude(float $value): Latitude
    {
        return new Latitude($value);
    }

    /**
     * @param float $value The longitude value in decimal degrees
     *
     * @throws InvalidCoordinatesException When longitude value is invalid
     */
    public static function createLongitude(float $value): Longitude
    {
        return new Longitude($value);
    }
}
