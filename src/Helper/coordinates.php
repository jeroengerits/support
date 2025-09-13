<?php

if (! function_exists('coordinates')) {
    /**
     * Create a Coordinates instance from various input types.
     *
     * @param float|string|array|int $latitude  The latitude value or array containing coordinates
     * @param float|int|string|null  $longitude The longitude value (required if latitude is not an array)
     *
     * @throws \JeroenGerits\Support\Exception\InvalidCoordinatesException
     * @throws \JeroenGerits\Support\Exception\InvalidLatitudeException
     * @throws \JeroenGerits\Support\Exception\InvalidLongitudeException
     */
    function coordinates(float|string|array|int $latitude, float|int|string|null $longitude = null): \JeroenGerits\Support\ValueObject\Coordinates
    {
        return match (true) {
            // Single array parameter
            is_array($latitude) && $longitude === null => \JeroenGerits\Support\ValueObject\Coordinates::fromArray($latitude),

            // Single string parameter (comma-separated)
            is_string($latitude) && $longitude === null => \JeroenGerits\Support\ValueObject\Coordinates::fromString($latitude),

            // Two numeric parameters
            (is_float($latitude) || is_int($latitude)) && (is_float($longitude) || is_int($longitude)) => \JeroenGerits\Support\ValueObject\Coordinates::fromFloats((float) $latitude, (float) $longitude),

            // Latitude as string, longitude as numeric
            is_string($latitude) && (is_float($longitude) || is_int($longitude)) => new \JeroenGerits\Support\ValueObject\Coordinates(
                \JeroenGerits\Support\ValueObject\Latitude::fromString($latitude),
                new \JeroenGerits\Support\ValueObject\Longitude((float) $longitude)
            ),

            // Latitude as numeric, longitude as string
            (is_float($latitude) || is_int($latitude)) && is_string($longitude) => new \JeroenGerits\Support\ValueObject\Coordinates(
                new \JeroenGerits\Support\ValueObject\Latitude((float) $latitude),
                \JeroenGerits\Support\ValueObject\Longitude::fromString($longitude)
            ),

            // Both as strings
            is_string($latitude) && is_string($longitude) => new \JeroenGerits\Support\ValueObject\Coordinates(
                \JeroenGerits\Support\ValueObject\Latitude::fromString($latitude),
                \JeroenGerits\Support\ValueObject\Longitude::fromString($longitude)
            ),

            default => throw new \JeroenGerits\Support\Exception\InvalidCoordinatesException('Invalid coordinates parameters provided')
        };
    }
}
