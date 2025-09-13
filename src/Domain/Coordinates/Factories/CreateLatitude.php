<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Factories;

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude;

/**
 * Action for creating Latitude instances.
 *
 * Provides static factory methods for creating Latitude instances
 * from various input types including strings and arrays.
 */
class CreateLatitude
{
    /**
     * Create latitude from various input types.
     *
     * @param  float|string|array $value The latitude value
     * @return Latitude           The latitude instance
     *
     * @throws InvalidLatitudeException If value is invalid
     */
    public static function from(float|string|array $value): Latitude
    {
        return match (true) {
            is_float($value) => new Latitude($value),
            is_string($value) => self::fromString($value),
            is_array($value) && isset($value['latitude']) => new Latitude((float) $value['latitude']),
            is_array($value) && isset($value[0]) => new Latitude((float) $value[0]),
            default => throw new InvalidLatitudeException('Invalid latitude value provided')
        };
    }

    /**
     * Create latitude from a string value.
     *
     * @param  string   $string The latitude string value
     * @return Latitude The latitude instance
     *
     * @throws InvalidLatitudeException If string is not numeric
     */
    public static function fromString(string $string): Latitude
    {
        if (! is_numeric($string)) {
            throw new InvalidLatitudeException("Invalid latitude string: {$string}");
        }

        return new Latitude((float) $string);
    }
}
