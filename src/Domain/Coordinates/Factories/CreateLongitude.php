<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Factories;

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude;

/**
 * Action for creating Longitude instances.
 *
 * Provides static factory methods for creating Longitude instances
 * from various input types including strings and arrays.
 */
class CreateLongitude
{
    /**
     * Create longitude from various input types.
     *
     * @param  float|string|array $value The longitude value
     * @return Longitude          The longitude instance
     *
     * @throws InvalidLongitudeException If value is invalid
     */
    public static function from(float|string|array $value): Longitude
    {
        return match (true) {
            is_float($value) => new Longitude($value),
            is_string($value) => self::fromString($value),
            is_array($value) && isset($value['longitude']) => new Longitude((float) $value['longitude']),
            is_array($value) && isset($value[0]) => new Longitude((float) $value[0]),
            default => throw new InvalidLongitudeException('Invalid longitude value provided')
        };
    }

    /**
     * Create longitude from a string value.
     *
     * @param  string    $string The longitude string value
     * @return Longitude The longitude instance
     *
     * @throws InvalidLongitudeException If string is not numeric
     */
    public static function fromString(string $string): Longitude
    {
        if (! is_numeric($string)) {
            throw new InvalidLongitudeException("Invalid longitude string: {$string}");
        }

        return new Longitude((float) $string);
    }
}
