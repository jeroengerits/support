<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

/**
 * Exception thrown when invalid coordinates are provided.
 */
class InvalidCoordinatesException extends BaseCoordinatesException
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
     */
    public static function latitudeOutOfRange(float $value): static
    {
        return new static(
            "Latitude value {$value} is outside the valid range of ".
            Latitude::MIN_LATITUDE.
            ' to '.
            Latitude::MAX_LATITUDE.
            ' degrees',
            self::CODE_OUT_OF_RANGE
        );
    }

    /**
     * @param  float  $value The longitude value that is out of range
     * @return static A new InvalidCoordinatesException instance
     */
    public static function longitudeOutOfRange(float $value): static
    {
        return new static(
            "Longitude value {$value} is outside the valid range of ".
            Longitude::MIN_LONGITUDE.
            ' to '.
            Longitude::MAX_LONGITUDE.
            ' degrees',
            self::CODE_OUT_OF_RANGE
        );
    }

    /**
     * @param  mixed  $value          The invalid value that was provided
     * @param  string $coordinateType The type of coordinate (latitude, longitude, or coordinate)
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidType(mixed $value, string $coordinateType = 'coordinate'): static
    {
        $expectedTypes = $coordinateType === 'latitude'
            ? 'float, int, string, or Latitude'
            : 'float, int, string, or Longitude';

        return new static(
            "Invalid {$coordinateType} type: ".gettype($value).". Expected: {$expectedTypes}",
            self::CODE_INVALID_TYPE
        );
    }

    /**
     * Create an exception for invalid coordinate format.
     *
     * @param  string $value          The invalid format string that was provided
     * @param  string $coordinateType The type of coordinate (latitude, longitude, or coordinate)
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidFormat(string $value, string $coordinateType = 'coordinate'): static
    {
        $example = $coordinateType === 'latitude' ? '40.7128' : '-74.0060';

        return new static(
            "Invalid {$coordinateType} format: '{$value}'. Expected decimal degrees (e.g., '{$example}')",
            self::CODE_INVALID_FORMAT
        );
    }

    /**
     * @param  array  $array      The array that was provided
     * @param  string $missingKey The key that was missing from the array
     * @return static A new InvalidCoordinatesException instance
     */
    public static function missingFromArray(array $array, string $missingKey): static
    {
        $availableKeys = implode(', ', array_keys($array));

        return new static(
            "{$missingKey} missing from coordinate array. Available keys: {$availableKeys}",
            self::CODE_MISSING_VALUE
        );
    }

    /**
     * @param  array  $array The array that has an invalid structure
     * @return static A new InvalidCoordinatesException instance
     */
    public static function invalidArrayStructure(array $array): static
    {
        return new static(
            'Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]',
            self::CODE_INVALID_FORMAT
        );
    }
}
