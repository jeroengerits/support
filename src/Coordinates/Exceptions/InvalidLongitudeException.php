<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

/**
 * Exception thrown when an invalid longitude value is provided.
 *
 * This exception is thrown when a longitude value is outside the valid range
 * of -180.0 to +180.0 degrees, or when an invalid type is provided.
 */
class InvalidLongitudeException extends BaseCoordinatesException
{
    /**
     * Create a new InvalidLongitudeException instance.
     *
     * @param string          $message          The exception message
     * @param int             $code             The exception code
     * @param mixed           $problematicValue The value that caused the exception
     * @param array           $context          Additional context about the error
     * @param \Exception|null $previous         The previous exception
     */
    public function __construct(
        string $message = 'Invalid longitude value provided',
        int $code = self::CODE_INVALID_VALUE,
        mixed $problematicValue = null,
        array $context = [],
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $problematicValue, $context, $previous);
    }

    /**
     * Create an exception for longitude values out of range.
     */
    public static function outOfRange(mixed $problematicValue = null, array $context = []): self
    {
        return new self(
            'Longitude value is outside the valid range of -180.0 to +180.0 degrees',
            self::CODE_OUT_OF_RANGE,
            $problematicValue,
            $context
        );
    }

    /**
     * Create an exception for invalid longitude type.
     */
    public static function invalidType(mixed $problematicValue = null, array $context = []): self
    {
        return new self(
            'Invalid longitude value type provided',
            self::CODE_INVALID_TYPE,
            $problematicValue,
            $context
        );
    }
}
