<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

/**
 * Exception thrown when invalid coordinates are provided.
 */
class InvalidCoordinatesException extends BaseCoordinatesException
{
    /**
     * Create a new InvalidCoordinatesException instance.
     */
    public function __construct(
        string $message = 'Invalid coordinates provided',
        int $code = self::CODE_INVALID_VALUE,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for latitude values outside the valid range.
     */
    public static function latitudeOutOfRange(float $value): static
    {
        return new static(
            "Latitude value {$value} is outside the valid range of -90.0 to +90.0 degrees",
            self::CODE_OUT_OF_RANGE
        );
    }

    /**
     * Create an exception for longitude values outside the valid range.
     */
    public static function longitudeOutOfRange(float $value): static
    {
        return new static(
            "Longitude value {$value} is outside the valid range of -180.0 to +180.0 degrees",
            self::CODE_OUT_OF_RANGE
        );
    }

    /**
     * Create an exception for invalid coordinate type.
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
     * Create an exception when coordinates are missing from array.
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
     * Create an exception for invalid array structure.
     */
    public static function invalidArrayStructure(array $array): static
    {
        return new static(
            'Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]',
            self::CODE_INVALID_FORMAT
        );
    }
}
