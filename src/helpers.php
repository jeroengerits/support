<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

if (! function_exists('coordinates')) {
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float|string|array|int|Latitude|null $latitude The latitude value
     * @param float|int|string|Longitude|null $longitude The longitude value (optional)
     * @return Coordinates
     * @throws InvalidLatitudeException
     * @throws InvalidLongitudeException
     */
    function coordinates(float|string|array|int|Latitude|null $latitude = null, float|int|string|Longitude|null $longitude = null): Coordinates
    {
        // Handle array input for latitude
        if (is_array($latitude)) {
            $lat = $latitude['lat'] ?? $latitude['latitude'] ?? $latitude[0] ?? null;
            $lng = $latitude['lng'] ?? $latitude['longitude'] ?? $latitude[1] ?? null;

            if ($lat === null || $lng === null) {
                throw new InvalidArgumentException('Array must contain latitude and longitude values');
            }

            return CoordinatesFactory::createCoordinates($lat, $lng);
        }

        return CoordinatesFactory::createCoordinates($latitude, $longitude);
    }
}

if (! function_exists('latitude')) {
    /**
     * Create a new Latitude instance from various input types.
     *
     * @param float|int|string|Latitude $value The latitude value
     *
     * @throws InvalidArgumentException|InvalidLatitudeException When latitude value is invalid
     */
    function latitude(float|int|string|Latitude $value): Latitude
    {
        return CoordinatesFactory::createLatitude($value);
    }
}

if (! function_exists('longitude')) {
    /**
     * Create a new Longitude instance from various input types.
     *
     * @param float|int|string|Longitude $value The longitude value
     *
     * @throws InvalidArgumentException|InvalidLongitudeException When longitude value is invalid
     */
    function longitude(float|int|string|Longitude $value): Longitude
    {
        return CoordinatesFactory::createLongitude($value);
    }
}
