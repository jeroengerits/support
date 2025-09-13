<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contract\Equatable;

class Coordinates implements \Stringable, Equatable
{
    /**
     * Create a new Coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value
     * @param Longitude $longitude The longitude value
     */
    public function __construct(public Latitude $latitude, public Longitude $longitude) {}

    /**
     * Get the string representation of the coordinates.
     *
     * @return string The coordinates as "latitude,longitude"
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    /**
     * Check if this coordinates object is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the coordinates are equal
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->longitude->isEqual($other->longitude) && $this->latitude->isEqual($other->latitude);
    }
}
