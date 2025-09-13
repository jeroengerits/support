<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

if (! function_exists('coordinates')) {
    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float|string|array|int $latitude  The latitude value
     * @param float|int|string|null  $longitude The longitude value (optional)
     */
    function coordinates(float|string|array|int $latitude, float|int|string|null $longitude = null): Coordinates
    {
        return CoordinatesFactory::createCoordinates($latitude, $longitude);
    }
}
