<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Domain\Coordinates\Factories\CreateCoordinates;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates;

if (! function_exists('coordinates')) {
    /**
     * @throws InvalidLongitudeException
     * @throws InvalidCoordinatesException
     * @throws InvalidLatitudeException
     */
    function coordinates(float|string|array|int $latitude, float|int|string|null $longitude = null): Coordinates
    {
        return CreateCoordinates::from($latitude, $longitude);
    }
}
