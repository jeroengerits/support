<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

/**
 * Exception thrown when invalid coordinates are provided.
 */
class InvalidCoordinatesException extends CoordinatesException
{
    /**
     * @param string         $message  The exception message
     * @param int            $code     The exception code
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message = 'Invalid coordinates provided',
        int $code = self::CODE_INVALID_VALUE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param  float  $value The latitude value that is out of range
     * @return static A new InvalidCoordinatesException instance
     *
     * @deprecated Use createOutOfRange() instead
     */
    public static function latitudeOutOfRange(float $value): static
    {
        return self::createOutOfRange(
            $value,
            'Latitude',
            Latitude::MIN_LATITUDE,
            Latitude::MAX_LATITUDE
        );
    }

    /**
     * Create an exception for coordinate values that are out of range.
     *
     * @param  float  $value          The coordinate value that is out of range
     * @param  string $coordinateType The type of coordinate (e.g., "Latitude", "Longitude")
     * @param  float  $minValue       The minimum valid value
     * @param  float  $maxValue       The maximum valid value
     * @return static A new InvalidCoordinatesException instance
     */
    public static function createOutOfRange(
        float $value,
        string $coordinateType,
        float $minValue,
        float $maxValue
    ): static {
        return new static(
            "{$coordinateType} value {$value} is outside the valid range of {$minValue} to {$maxValue} degrees",
            self::CODE_OUT_OF_RANGE
        );
    }

    /**
     * @param  float  $value The longitude value that is out of range
     * @return static A new InvalidCoordinatesException instance
     *
     * @deprecated Use createOutOfRange() instead
     */
    public static function longitudeOutOfRange(float $value): static
    {
        return self::createOutOfRange(
            $value,
            'Longitude',
            Longitude::MIN_LONGITUDE,
            Longitude::MAX_LONGITUDE
        );
    }

    /**
     * Create an exception for invalid coordinate data types.
     *
     * @param  mixed  $value          The invalid value that was provided
     * @param  string $coordinateType The type of coordinate (latitude, longitude, or coordinate)
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidType(mixed $value, string $coordinateType = 'coordinate'): static
    {
        $expectedTypes = match ($coordinateType) {
            'latitude' => 'float, int, string, or Latitude',
            'longitude' => 'float, int, string, or Longitude',
            default => 'float, int, string, or coordinate object',
        };

        $actualType = get_debug_type($value);

        return new static(
            "Invalid {$coordinateType} type: {$actualType}. Expected: {$expectedTypes}",
            self::CODE_INVALID_TYPE
        );
    }

    /**
     * Create an exception for invalid coordinate format strings.
     *
     * @param  string $value          The invalid format string that was provided
     * @param  string $coordinateType The type of coordinate (latitude, longitude, or coordinate)
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidFormat(string $value, string $coordinateType = 'coordinate'): static
    {
        $example = match ($coordinateType) {
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
            default => '40.7128,-74.0060',
        };

        return new static(
            "Invalid {$coordinateType} format: '{$value}'. Expected decimal degrees (e.g., '{$example}')",
            self::CODE_INVALID_FORMAT
        );
    }

    /**
     * Create an exception for missing required keys in coordinate arrays.
     *
     * @param  array  $array      The array that was provided
     * @param  string $missingKey The key that was missing from the array
     * @return static A new InvalidCoordinatesException instance
     */
    public static function missingFromArray(array $array, string $missingKey): static
    {
        $availableKeys = implode(', ', array_keys($array));
        $keyCount = count($array);

        return new static(
            "Required key '{$missingKey}' missing from coordinate array. ".
            "Available keys: [{$availableKeys}] ({$keyCount} total)",
            self::CODE_MISSING_VALUE
        );
    }

    /**
     * Create an exception for invalid coordinate array structures.
     *
     * @param  array  $array The array that has an invalid structure
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidArrayStructure(array $array): static
    {
        $arraySize = count($array);
        $arrayKeys = implode(', ', array_keys($array));

        return new static(
            'Invalid coordinate array structure. '.
            'Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]. '.
            "Got: array with {$arraySize} elements and keys [{$arrayKeys}]",
            self::CODE_INVALID_FORMAT
        );
    }

    /**
     * Create an exception for invalid numeric values.
     *
     * @param  mixed  $value          The invalid value
     * @param  string $coordinateType The type of coordinate
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidNumericValue(mixed $value, string $coordinateType = 'coordinate'): static
    {
        $actualType = get_debug_type($value);

        return new static(
            "Invalid {$coordinateType} numeric value: '{$value}' (type: {$actualType}). ".
            'Expected a valid numeric value that can be converted to float',
            self::CODE_INVALID_TYPE
        );
    }

    /**
     * Create an exception for empty coordinate values.
     *
     * @param  string $coordinateType The type of coordinate
     * @return static A new InvalidCoordinatesException instance
     */
    public static function emptyValue(string $coordinateType = 'coordinate'): static
    {
        return new static(
            "Empty {$coordinateType} value provided. ".
            'Coordinate values cannot be empty or null',
            self::CODE_MISSING_VALUE
        );
    }
}
