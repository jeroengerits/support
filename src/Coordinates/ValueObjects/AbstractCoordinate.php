<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

/**
 * Abstract base class for coordinate value objects.
 *
 * This class provides common functionality for latitude and longitude
 * value objects, including validation, string representation, and equality comparison.
 *
 * @template T of self
 */
abstract class AbstractCoordinate implements Equatable, Stringable
{
    /** @var float The coordinate value in decimal degrees */
    public readonly float $value;

    /**
     * Create a new coordinate instance.
     *
     * @param float $value The coordinate value in decimal degrees
     *
     * @throws InvalidCoordinatesException When the coordinate value is outside the valid range
     */
    public function __construct(float $value)
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    /**
     * Create a new coordinate instance from a value.
     *
     * @param  float  $value The coordinate value in decimal degrees
     * @return static The new coordinate instance
     *
     * @throws InvalidCoordinatesException When the coordinate value is invalid
     */
    public static function create(float $value): static
    {
        return new static($value);
    }

    /**
     * Get the string representation of the coordinate.
     *
     * @return string The coordinate value as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Get the coordinate value as a string.
     *
     * @return string The coordinate value as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check if this coordinate is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the coordinates are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof static) {
            return false;
        }

        return $this->value === $other->value;
    }

    /**
     * Validate the coordinate value.
     *
     * @param float $value The coordinate value to validate
     *
     * @throws InvalidCoordinatesException When the coordinate value is outside the valid range
     */
    abstract protected function validateValue(float $value): void;

    /**
     * Get the minimum valid value for this coordinate type.
     *
     * @return float The minimum valid value
     */
    abstract protected function getMinValue(): float;

    /**
     * Get the maximum valid value for this coordinate type.
     *
     * @return float The maximum valid value
     */
    abstract protected function getMaxValue(): float;

    /**
     * Get the name of this coordinate type for error messages.
     *
     * @return string The coordinate type name
     */
    abstract protected function getCoordinateTypeName(): string;

    /**
     * Create an exception for out-of-range values.
     *
     * @param  float                       $value The out-of-range value
     * @return InvalidCoordinatesException The exception instance
     */
    protected function createOutOfRangeException(float $value): InvalidCoordinatesException
    {
        $minValue = $this->getMinValue();
        $maxValue = $this->getMaxValue();
        $typeName = $this->getCoordinateTypeName();

        return InvalidCoordinatesException::createOutOfRange($value, $typeName, $minValue, $maxValue);
    }
}
