<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

/**
 * Exception thrown when invalid coordinates are provided.
 *
 * This exception is thrown when coordinate values are invalid, missing,
 * or in an incorrect format. It can be thrown for both latitude and
 * longitude issues or when the coordinate pair is malformed.
 */
class InvalidCoordinatesException extends BaseCoordinatesException
{
    /**
     * Create a new InvalidCoordinatesException instance.
     *
     * @param string          $message          The exception message
     * @param int             $code             The exception code
     * @param mixed           $problematicValue The value that caused the exception
     * @param array           $context          Additional context about the error
     * @param \Exception|null $previous         The previous exception
     */
    public function __construct(
        string $message = 'Invalid coordinates values provided',
        int $code = self::CODE_INVALID_VALUE,
        mixed $problematicValue = null,
        array $context = [],
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $problematicValue, $context, $previous);
    }

    /**
     * Create an exception for missing coordinate values.
     */
    public static function missingValues(mixed $problematicValue = null, array $context = []): self
    {
        return new self(
            'Coordinate values are missing or incomplete',
            self::CODE_MISSING_VALUE,
            $problematicValue,
            $context
        );
    }

    /**
     * Create an exception for invalid coordinate format.
     */
    public static function invalidFormat(mixed $problematicValue = null, array $context = []): self
    {
        return new self(
            'Invalid coordinate format provided',
            self::CODE_INVALID_FORMAT,
            $problematicValue,
            $context
        );
    }
}
