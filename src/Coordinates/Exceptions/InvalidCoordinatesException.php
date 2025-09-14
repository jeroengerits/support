<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

/**
 * Exception thrown when invalid coordinates are provided.
 *
 * This exception is thrown when coordinate values are invalid, out of range,
 * have incorrect types, or are missing from required data structures.
 * It provides static factory methods for common error scenarios.
 *
 * @package JeroenGerits\Support\Coordinates\Exceptions
 * @since   1.0.0
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
 *
 * // Throw for latitude out of range
 * throw InvalidCoordinatesException::latitudeOutOfRange(95.0);
 *
 * // Throw for longitude out of range
 * throw InvalidCoordinatesException::longitudeOutOfRange(185.0);
 *
 * // Throw for invalid type
 * throw InvalidCoordinatesException::invalidType(['invalid'], 'latitude');
 *
 * // Throw for missing value in array
 * throw InvalidCoordinatesException::missingFromArray(['lat' => 40.0], 'longitude');
 * ```
 */
class InvalidCoordinatesException extends BaseCoordinatesException
{
    /**
     * Create a new InvalidCoordinatesException instance.
     *
     * @param string          $message  The exception message
     * @param int             $code     The exception code
     * @param \Exception|null $previous The previous exception for chaining
     *
     * @example
     * ```php
     * // Basic exception
     * $exception = new InvalidCoordinatesException();
     *
     * // Custom message
     * $exception = new InvalidCoordinatesException('Custom coordinate error');
     *
     * // With specific error code
     * $exception = new InvalidCoordinatesException(
     *     'Invalid coordinate format',
     *     InvalidCoordinatesException::CODE_INVALID_FORMAT
     * );
     * ```
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
     *
     * @param  float  $value The latitude value that is out of range
     * @return static A new InvalidCoordinatesException instance
     *
     * @example
     * ```php
     * // Latitude too high
     * throw InvalidCoordinatesException::latitudeOutOfRange(95.0);
     * // Message: "Latitude value 95.0 is outside the valid range of -90.0 to +90.0 degrees"
     *
     * // Latitude too low
     * throw InvalidCoordinatesException::latitudeOutOfRange(-95.0);
     * // Message: "Latitude value -95.0 is outside the valid range of -90.0 to +90.0 degrees"
     * ```
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
     *
     * @param  float  $value The longitude value that is out of range
     * @return static A new InvalidCoordinatesException instance
     *
     * @example
     * ```php
     * // Longitude too high
     * throw InvalidCoordinatesException::longitudeOutOfRange(185.0);
     * // Message: "Longitude value 185.0 is outside the valid range of -180.0 to +180.0 degrees"
     *
     * // Longitude too low
     * throw InvalidCoordinatesException::longitudeOutOfRange(-185.0);
     * // Message: "Longitude value -185.0 is outside the valid range of -180.0 to +180.0 degrees"
     * ```
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
     *
     * @param  mixed  $value          The invalid value that was provided
     * @param  string $coordinateType The type of coordinate (latitude, longitude, or coordinate)
     * @return static A new InvalidCoordinatesException instance
     *
     * @example
     * ```php
     * // Invalid latitude type
     * throw InvalidCoordinatesException::invalidType(['invalid'], 'latitude');
     * // Message: "Invalid latitude type: array. Expected: float, int, string, or Latitude"
     *
     * // Invalid longitude type
     * throw InvalidCoordinatesException::invalidType(null, 'longitude');
     * // Message: "Invalid longitude type: NULL. Expected: float, int, string, or Longitude"
     *
     * // Invalid coordinate type (default)
     * throw InvalidCoordinatesException::invalidType(new stdClass());
     * // Message: "Invalid coordinate type: object. Expected: float, int, string, or Longitude"
     * ```
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
     *
     * @example
     * ```php
     * // Invalid latitude format
     * throw InvalidCoordinatesException::invalidFormat('40° 42\' 46" N', 'latitude');
     * // Message: "Invalid latitude format: '40° 42\' 46" N'. Expected decimal degrees (e.g., '40.7128')"
     *
     * // Invalid longitude format
     * throw InvalidCoordinatesException::invalidFormat('74° 0\' 22" W', 'longitude');
     * // Message: "Invalid longitude format: '74° 0\' 22" W'. Expected decimal degrees (e.g., '-74.0060')"
     *
     * // Invalid coordinate format (default)
     * throw InvalidCoordinatesException::invalidFormat('not a number');
     * // Message: "Invalid coordinate format: 'not a number'. Expected decimal degrees (e.g., '-74.0060')"
     * ```
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
     *
     * @param  array  $array      The array that was provided
     * @param  string $missingKey The key that was missing from the array
     * @return static A new InvalidCoordinatesException instance
     *
     * @example
     * ```php
     * // Missing longitude from array
     * throw InvalidCoordinatesException::missingFromArray(['lat' => 40.7128], 'longitude');
     * // Message: "longitude missing from coordinate array. Available keys: lat"
     *
     * // Missing latitude from array
     * throw InvalidCoordinatesException::missingFromArray(['lng' => -74.0060], 'latitude');
     * // Message: "latitude missing from coordinate array. Available keys: lng"
     *
     * // Missing from empty array
     * throw InvalidCoordinatesException::missingFromArray([], 'latitude');
     * // Message: "latitude missing from coordinate array. Available keys: "
     * ```
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
     *
     * @param  array  $array The array that has an invalid structure
     * @return static A new InvalidCoordinatesException instance
     *
     * @example
     * ```php
     * // Empty array
     * throw InvalidCoordinatesException::invalidArrayStructure([]);
     * // Message: "Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]"
     *
     * // Array with wrong keys
     * throw InvalidCoordinatesException::invalidArrayStructure(['x' => 40.0, 'y' => -74.0]);
     * // Message: "Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]"
     *
     * // Array with only one coordinate
     * throw InvalidCoordinatesException::invalidArrayStructure(['lat' => 40.0]);
     * // Message: "Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]"
     * ```
     */
    public static function invalidArrayStructure(array $array): static
    {
        return new static(
            'Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]',
            self::CODE_INVALID_FORMAT
        );
    }
}
