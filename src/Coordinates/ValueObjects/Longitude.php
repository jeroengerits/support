<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

class Longitude implements Equatable, Stringable
{
    /**
     * Create a new Longitude instance.
     *
     * @param float $value The longitude value in decimal degrees (-180.0 to +180.0)
     *
     * @throws InvalidCoordinatesException When longitude value is outside valid range
     */
    public function __construct(public float $value)
    {
        if ($value < -180.0 || $value > 180.0) {
            throw InvalidCoordinatesException::longitudeOutOfRange($value);
        }
    }

    /**
     * Get the string representation of the longitude.
     *
     * @return string The longitude as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert the longitude value to a string.
     *
     * @return string The longitude value as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check if this longitude is equal to another.
     *
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
