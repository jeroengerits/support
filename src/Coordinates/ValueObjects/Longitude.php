<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing a longitude coordinate.
 */
class Longitude implements Equatable, Stringable
{
    /** Minimum valid longitude value in decimal degrees. */
    public const float MIN_LONGITUDE = -180.0;

    /** Maximum valid longitude value in decimal degrees. */
    public const float MAX_LONGITUDE = 180.0;

    /**
     * @param float $value The longitude value in decimal degrees (-180.0 to +180.0)
     *
     * @throws InvalidCoordinatesException When longitude value is outside valid range
     */
    public function __construct(public float $value)
    {
        if ($value < self::MIN_LONGITUDE || $value > self::MAX_LONGITUDE) {
            throw InvalidCoordinatesException::longitudeOutOfRange($value);
        }
    }

    /**
     * Create a new Longitude instance from a value.
     *
     * @param float $value The longitude value in decimal degrees
     *
     * @throws InvalidCoordinatesException When longitude value is invalid
     */
    public static function create(float $value): self
    {
        return new self($value);
    }

    /**
     * @return string The longitude as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string The longitude value as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @param  Equatable $other The other object to compare
     * @return bool      True if the longitudes are equal
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }
}
