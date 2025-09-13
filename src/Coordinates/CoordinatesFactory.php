<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

class CoordinatesFactory
{
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float|string|int|Latitude  $latitude  The latitude value (float, string, int, or Latitude instance)
     * @param float|string|int|Longitude $longitude The longitude value (float, string, int, or Longitude instance)
     *
     * @throws \InvalidArgumentException When latitude or longitude values are invalid
     */
    public static function createCoordinates($latitude, $longitude): Coordinates
    {
        return new Coordinates(
            match (true) {
                $latitude instanceof Latitude => $latitude,
                is_string($latitude) => new Latitude((float) $latitude),
                is_float($latitude) => new Latitude($latitude),
                is_int($latitude) => new Latitude((float) $latitude),
                default => throw new \InvalidArgumentException('Invalid latitude value'),
            },
            match (true) {
                $longitude instanceof Longitude => $longitude,
                is_string($longitude) => new Longitude((float) $longitude),
                is_float($longitude) => new Longitude($longitude),
                is_int($longitude) => new Longitude((float) $longitude),
                default => throw new \InvalidArgumentException('Invalid longitude value'),
            }
        );
    }
}
