<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing a latitude coordinate.
 */
class Latitude implements Equatable, Stringable
{
    /** Minimum valid latitude value in decimal degrees. */
    public const float MIN_LATITUDE = -90.0;

    /** Maximum valid latitude value in decimal degrees. */
    public const float MAX_LATITUDE = 90.0;

    /**
     * @param float $value The latitude value in decimal degrees (-90.0 to +90.0)
     *
     * @throws InvalidCoordinatesException When latitude value is outside valid range
     */
    public function __construct(public float $value)
    {
        if ($value < self::MIN_LATITUDE || $value > self::MAX_LATITUDE) {
            throw InvalidCoordinatesException::latitudeOutOfRange($value);
        }
    }

    /**
     * @return string The latitude as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string The latitude value as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @param  Equatable $other The other object to compare
     * @return bool      True if the latitudes are equal
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }
}
