<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Factories;

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude;

/**
 * Action for creating Coordinates instances.
 *
 * Provides static factory methods for creating Coordinates instances
 * from various input types including strings, arrays, and numeric values.
 */
class CreateCoordinates
{
    /**
     * Create coordinates from various input types.
     *
     * @param  float|string|array|int $latitude  The latitude value
     * @param  float|int|string|null  $longitude The longitude value (optional for single parameter)
     * @return Coordinates            The coordinates instance
     *
     * @throws InvalidCoordinatesException If parameters are invalid
     * @throws InvalidLatitudeException
     * @throws InvalidLongitudeException
     */
    public static function from(float|string|array|int $latitude, float|int|string|null $longitude = null): Coordinates
    {
        return match (true) {
            // Single array parameter
            is_array($latitude) && $longitude === null => self::fromArray($latitude),

            // Single string parameter (comma-separated)
            is_string($latitude) && $longitude === null => self::fromString($latitude),

            // Two numeric parameters
            (is_float($latitude) || is_int($latitude)) && (is_float($longitude) || is_int($longitude)) => self::fromFloats((float) $latitude, (float) $longitude),

            // Latitude as string, longitude as numeric
            is_string($latitude) && (is_float($longitude) || is_int($longitude)) => new Coordinates(
                CreateLatitude::fromString($latitude),
                new Longitude((float) $longitude)
            ),

            // Latitude as numeric, longitude as string
            (is_float($latitude) || is_int($latitude)) && is_string($longitude) => new Coordinates(
                new Latitude((float) $latitude),
                CreateLongitude::fromString($longitude)
            ),

            // Both as strings
            is_string($latitude) && is_string($longitude) => new Coordinates(
                CreateLatitude::fromString($latitude),
                CreateLongitude::fromString($longitude)
            ),

            default => throw new InvalidCoordinatesException('Invalid coordinates parameters provided')
        };
    }

    /**
     * Create coordinates from an array.
     *
     * @param  array<string, mixed> $coordinates Array with 'latitude' and 'longitude' keys
     * @return Coordinates          The coordinates instance
     *
     * @throws InvalidCoordinatesException If required keys are missing
     * @throws InvalidLatitudeException
     * @throws InvalidLongitudeException
     */
    public static function fromArray(array $coordinates): Coordinates
    {
        if (! isset($coordinates['latitude']) || ! isset($coordinates['longitude'])) {
            throw new InvalidCoordinatesException('Array must contain both latitude and longitude keys');
        }

        return new Coordinates(
            new Latitude($coordinates['latitude']),
            new Longitude($coordinates['longitude'])
        );
    }

    /**
     * Create coordinates from a string.
     *
     * @param  string      $string String in format 'latitude,longitude'
     * @return Coordinates The coordinates instance
     *
     * @throws InvalidCoordinatesException If format is invalid
     * @throws InvalidLatitudeException
     * @throws InvalidLongitudeException
     */
    public static function fromString(string $string): Coordinates
    {
        $parts = explode(',', $string);

        if (count($parts) !== 2) {
            throw new InvalidCoordinatesException('String must contain exactly one comma separating latitude and longitude');
        }

        $latitude = trim($parts[0]);
        $longitude = trim($parts[1]);

        if ($latitude === '' || $latitude === '0' || ($longitude === '' || $longitude === '0')) {
            throw new InvalidCoordinatesException('Latitude and longitude cannot be empty');
        }

        return new Coordinates(
            CreateLatitude::fromString($latitude),
            CreateLongitude::fromString($longitude)
        );
    }

    /**
     * Create coordinates from float values.
     *
     * @param  float       $latitude  The latitude value
     * @param  float       $longitude The longitude value
     * @return Coordinates The coordinates instance
     *
     * @throws InvalidLatitudeException
     * @throws InvalidLongitudeException
     */
    public static function fromFloats(float $latitude, float $longitude): Coordinates
    {
        return new Coordinates(
            new Latitude($latitude),
            new Longitude($longitude)
        );
    }
}
